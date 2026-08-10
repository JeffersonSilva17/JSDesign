'use client';

import { type FormEvent, type KeyboardEvent, useEffect, useId, useRef, useState } from 'react';

import { publicContent } from '@/features/public-store/publicLayoutContent';
import type { FirstPurchaseOfferPayload, FirstPurchaseCouponPayload } from '@/bff/apiClient';

type RequestState = 'idle' | 'submitting' | 'success' | 'error';

const STORAGE_KEY = 'jsdesign:first-purchase-discount:v1:viewed';
const SURFACE_QUERY_EVENT = 'jsdesign:global-surface-query:v1';
const SURFACE_RELEASE_EVENT = 'jsdesign:global-surface-release:v1';
let memorySuppressed = false;

type SurfaceQueryDetail = { locked: boolean };

function hasSessionSuppression(): boolean {
  if (memorySuppressed) {
    return true;
  }

  try {
    return window.sessionStorage.getItem(STORAGE_KEY) === '1';
  } catch {
    return false;
  }
}

function markSessionSuppressed(): void {
  memorySuppressed = true;

  try {
    window.sessionStorage.setItem(STORAGE_KEY, '1');
  } catch {
    // Memory suppression above is the privacy-preserving fallback for this tab.
  }
}

function editableElementIsFocused(): boolean {
  const activeElement = document.activeElement;

  if (!activeElement) {
    return false;
  }

  const tagName = activeElement.tagName.toLowerCase();

  return (
    tagName === 'input' ||
    tagName === 'textarea' ||
    tagName === 'select' ||
    activeElement.getAttribute('contenteditable') === 'true'
  );
}

function scrollEngagementReached(): boolean {
  const scrollableDistance = Math.max(
    document.documentElement.scrollHeight - window.innerHeight,
    1,
  );

  return window.scrollY / scrollableDistance >= 0.22;
}

function formatBenefit(offer: FirstPurchaseOfferPayload): string {
  const minimum =
    offer.minimum_amount === null
      ? 'sem valor mínimo'
      : `mínimo configurado de ${new Intl.NumberFormat('pt-BR', {
          style: 'currency',
          currency: 'EUR',
        }).format(offer.minimum_amount)}`;

  return `${offer.discount_percent}% na primeira compra, ${minimum}, sujeito à validação de elegibilidade.`;
}

export function FirstPurchaseDiscountDialog() {
  const content = publicContent.firstPurchaseDiscount;
  const titleId = useId();
  const descriptionId = useId();
  const emailErrorId = useId();
  const emailHelpId = useId();
  const authorizationErrorId = useId();
  const statusId = useId();
  const dialogRef = useRef<HTMLDialogElement>(null);
  const closeButtonRef = useRef<HTMLButtonElement>(null);
  const previousFocusRef = useRef<HTMLElement | null>(null);
  const emailRef = useRef<HTMLInputElement>(null);
  const authorizationRef = useRef<HTMLInputElement>(null);
  const submitInFlightRef = useRef(false);
  const ownsSurfaceLockRef = useRef(false);
  const [offer, setOffer] = useState<FirstPurchaseOfferPayload | null>(null);
  const [inlineFallback, setInlineFallback] = useState(false);
  const [dismissed, setDismissed] = useState(false);
  const [isOpen, setIsOpen] = useState(false);
  const [email, setEmail] = useState('');
  const [authorizationAccepted, setAuthorizationAccepted] = useState(false);
  const [state, setState] = useState<RequestState>('idle');
  const [emailError, setEmailError] = useState('');
  const [authorizationError, setAuthorizationError] = useState('');
  const [statusMessage, setStatusMessage] = useState('');
  const [couponCode, setCouponCode] = useState('');
  const [copyLabel, setCopyLabel] = useState<string>(content.copy);

  useEffect(() => {
    const answerSurfaceQuery = (event: Event) => {
      if (ownsSurfaceLockRef.current) {
        (event as CustomEvent<SurfaceQueryDetail>).detail.locked = true;
      }
    };

    window.addEventListener(SURFACE_QUERY_EVENT, answerSurfaceQuery);

    return () => {
      window.removeEventListener(SURFACE_QUERY_EVENT, answerSurfaceQuery);
      releaseSurfaceLock();
    };
  }, []);

  useEffect(() => {
    let active = true;

    async function loadOffer() {
      try {
        const response = await fetch('/api/promotions/first-purchase-offer', {
          headers: {
            accept: 'application/json',
          },
          cache: 'no-store',
        });
        const payload = (await response.json()) as FirstPurchaseOfferPayload;

        if (active && payload.enabled === true) {
          setOffer(payload);
        }
      } catch {
        if (active) {
          setOffer(null);
        }
      }
    }

    void loadOffer();

    return () => {
      active = false;
    };
  }, []);

  useEffect(() => {
    if (!offer || hasSessionSuppression()) {
      return;
    }

    if (typeof HTMLDialogElement === 'undefined' || !dialogRef.current?.showModal) {
      markSessionSuppressed();
      setInlineFallback(true);
      return;
    }

    let timer: number | null = null;
    let decided = false;
    let userIntentObserved = false;
    let eligible = false;

    const clearPendingTimer = () => {
      if (timer !== null) {
        window.clearTimeout(timer);
        timer = null;
      }
    };

    const openWhenSafe = () => {
      timer = null;

      if (
        decided ||
        document.visibilityState !== 'visible' ||
        editableElementIsFocused() ||
        document.querySelector('dialog[open]')
      ) {
        return;
      }

      const dialog = dialogRef.current;

      if (!dialog || dialog.open) {
        return;
      }

      if (!tryAcquireSurfaceLock()) {
        return;
      }

      previousFocusRef.current = document.activeElement instanceof HTMLElement ? document.activeElement : null;
      try {
        dialog.showModal();
      } catch {
        releaseSurfaceLock();
        return;
      }

      decided = true;
      markSessionSuppressed();
      setIsOpen(true);
      window.setTimeout(() => closeButtonRef.current?.focus(), 0);
    };

    const scheduleOpen = () => {
      if (!eligible || decided || timer !== null) {
        return;
      }

      timer = window.setTimeout(openWhenSafe, 700);
    };

    const handleScroll = () => {
      if (userIntentObserved && scrollEngagementReached()) {
        eligible = true;
        scheduleOpen();
      }
    };

    const handleIntent = (event: Event) => {
      const target = event.target;

      if (target instanceof HTMLElement) {
        const tagName = target.tagName.toLowerCase();

        if (tagName === 'input' || tagName === 'textarea' || tagName === 'select') {
          return;
        }
      }

      userIntentObserved = true;

      const scrollableDistance = document.documentElement.scrollHeight - window.innerHeight;

      if (scrollableDistance <= 1) {
        eligible = true;
        scheduleOpen();
      }
    };

    const handleVisibilityOrRelease = () => {
      if (eligible && document.visibilityState === 'visible') {
        scheduleOpen();
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('wheel', handleIntent, { passive: true });
    window.addEventListener('touchmove', handleIntent, { passive: true });
    window.addEventListener('pointerdown', handleIntent);
    window.addEventListener('keydown', handleIntent);
    document.addEventListener('visibilitychange', handleVisibilityOrRelease);
    document.addEventListener('focusout', handleVisibilityOrRelease);
    window.addEventListener(SURFACE_RELEASE_EVENT, handleVisibilityOrRelease);

    return () => {
      decided = true;
      clearPendingTimer();
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('wheel', handleIntent);
      window.removeEventListener('touchmove', handleIntent);
      window.removeEventListener('pointerdown', handleIntent);
      window.removeEventListener('keydown', handleIntent);
      document.removeEventListener('visibilitychange', handleVisibilityOrRelease);
      document.removeEventListener('focusout', handleVisibilityOrRelease);
      window.removeEventListener(SURFACE_RELEASE_EVENT, handleVisibilityOrRelease);
    };
  }, [offer]);

  function closeDialog() {
    const dialog = dialogRef.current;

    if (dialog?.open) {
      dialog.close();
    }

    releaseSurfaceLock();
    setIsOpen(false);
    setDismissed(true);
    const focusTarget = previousFocusRef.current ?? document.querySelector<HTMLElement>('main a, main button, main');
    focusTarget?.focus();
  }

  function tryAcquireSurfaceLock(): boolean {
    const detail: SurfaceQueryDetail = { locked: false };
    window.dispatchEvent(new CustomEvent<SurfaceQueryDetail>(SURFACE_QUERY_EVENT, { detail }));

    if (detail.locked) {
      return false;
    }

    ownsSurfaceLockRef.current = true;

    return true;
  }

  function releaseSurfaceLock(): void {
    if (!ownsSurfaceLockRef.current) {
      return;
    }

    ownsSurfaceLockRef.current = false;
    window.dispatchEvent(new Event(SURFACE_RELEASE_EVENT));
  }

  function validateForm(): boolean {
    const trimmedEmail = email.trim();
    const emailInvalid = !trimmedEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail);
    let valid = true;

    setEmailError('');
    setAuthorizationError('');

    if (!trimmedEmail) {
      setEmailError(content.errors.emailRequired);
      valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
      setEmailError(content.errors.emailInvalid);
      valid = false;
    }

    if (!authorizationAccepted) {
      setAuthorizationError(content.errors.authorizationRequired);
      valid = false;
    }

    if (!valid) {
      window.setTimeout(() => {
        if (emailInvalid) {
          emailRef.current?.focus();
        } else {
          authorizationRef.current?.focus();
        }
      }, 0);
    }

    return valid;
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    if (!offer || submitInFlightRef.current) {
      return;
    }

    if (!validateForm()) {
      setState('error');
      return;
    }

    submitInFlightRef.current = true;
    setState('submitting');
    setStatusMessage('');
    setCouponCode('');

    try {
      const response = await fetch('/api/promotions/first-purchase-coupons', {
        method: 'POST',
        headers: {
          accept: 'application/json',
          'content-type': 'application/json',
        },
        cache: 'no-store',
        body: JSON.stringify({
          email: email.trim(),
          authorization_accepted: true,
          authorization_text_version: offer.authorization_text_version,
          offer_id: offer.offer_id,
        }),
      });
      const payload = (await response.json()) as FirstPurchaseCouponPayload;

      if (payload.status === 'validation_error') {
        const nextEmailError = payload.errors.email?.[0] ?? '';
        const nextAuthorizationError = payload.errors.authorization_accepted?.[0] ?? '';
        setEmailError(nextEmailError);
        setAuthorizationError(nextAuthorizationError);
        setState('error');
        setStatusMessage(
          payload.errors.authorization_text_version?.[0] ??
            payload.errors.offer_id?.[0] ??
            payload.message,
        );
        window.setTimeout(() => {
          if (nextEmailError) {
            emailRef.current?.focus();
          } else if (nextAuthorizationError) {
            authorizationRef.current?.focus();
          }
        }, 0);
        return;
      }

      if (!response.ok || payload.status !== 'accepted') {
        setState('error');
        setStatusMessage(payload.message || content.errors.retry);
        return;
      }

      setState('success');
      setStatusMessage(payload.message);
      setCouponCode(payload.delivery === 'display' ? payload.coupon_code : '');
    } catch {
      setState('error');
      setStatusMessage(content.errors.retry);
    } finally {
      submitInFlightRef.current = false;
    }
  }

  async function copyCoupon() {
    if (!couponCode) {
      return;
    }

    try {
      await navigator.clipboard.writeText(couponCode);
      setCopyLabel(content.copied);
      closeDialog();
    } catch {
      setCopyLabel(content.copy);
    }
  }

  function trapDialogFocus(event: KeyboardEvent<HTMLDialogElement>): void {
    if (event.key !== 'Tab') {
      return;
    }

    const dialog = dialogRef.current;

    if (!dialog) {
      return;
    }

    const focusable = Array.from(
      dialog.querySelectorAll<HTMLElement>(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
      ),
    ).filter((element) => element.getClientRects().length > 0);
    const first = focusable[0];
    const last = focusable.at(-1);

    if (!first || !last) {
      event.preventDefault();
      dialog.focus();
      return;
    }

    if (event.shiftKey && (document.activeElement === first || document.activeElement === dialog)) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  if (!offer || dismissed) {
    return null;
  }

  const offerTexts = offer.texts;

  if (!offerTexts) {
    return null;
  }

  const form = (
    <form className="discount-dialog__form" noValidate onSubmit={handleSubmit}>
      <p className="discount-dialog__benefit">{formatBenefit(offer)}</p>
      <p className="discount-dialog__manual">{content.manualUse}</p>

      <div className="discount-dialog__field">
        <label htmlFor="first-purchase-email">{content.fieldLabel}</label>
        <input
          ref={emailRef}
          id="first-purchase-email"
          autoComplete="email"
          type="email"
          value={email}
          aria-describedby={`${emailHelpId}${emailError ? ` ${emailErrorId}` : ''}`}
          aria-invalid={emailError ? 'true' : 'false'}
          onChange={(event) => setEmail(event.target.value)}
        />
        <p id={emailHelpId} className="discount-dialog__help">
          {content.fieldHelp}
        </p>
        {emailError ? (
          <p id={emailErrorId} className="discount-dialog__error">
            {emailError}
          </p>
        ) : null}
      </div>

      <div className="discount-dialog__authorization">
        <input
          ref={authorizationRef}
          id="first-purchase-authorization"
          type="checkbox"
          checked={authorizationAccepted}
          aria-describedby={authorizationError ? authorizationErrorId : undefined}
          aria-invalid={authorizationError ? 'true' : 'false'}
          onChange={(event) => setAuthorizationAccepted(event.target.checked)}
        />
        <label htmlFor="first-purchase-authorization">{offerTexts.authorization}</label>
      </div>
      {authorizationError ? (
        <p id={authorizationErrorId} className="discount-dialog__error">
          {authorizationError}
        </p>
      ) : null}

      <a className="discount-dialog__privacy" href={offerTexts.privacy_url}>
        Privacidade
      </a>

      <div
        id={statusId}
        className="discount-dialog__status"
        role="status"
        aria-label={content.statusLabel}
      >
        {statusMessage}
      </div>

      {couponCode ? (
        <div className="discount-dialog__coupon" aria-live="polite">
          <code>{couponCode}</code>
          <button className="button button--secondary" type="button" onClick={copyCoupon}>
            {copyLabel}
          </button>
        </div>
      ) : null}

      <div className="discount-dialog__actions">
        {state === 'success' && offer.delivery_mode === 'email' ? (
          <button className="button button--primary" type="button" onClick={closeDialog}>
            {content.confirm}
          </button>
        ) : (
          <button className="button button--primary" type="submit" disabled={state === 'submitting'}>
            {state === 'submitting' ? content.submitting : content.submit}
          </button>
        )}
        <button className="button button--secondary" type="button" onClick={closeDialog}>
          {content.decline}
        </button>
      </div>
    </form>
  );

  if (inlineFallback) {
    return (
      <section className="discount-inline" aria-labelledby={titleId}>
        <div className="discount-inline__inner">
          <h2 id={titleId}>{offerTexts.title}</h2>
          <p>{offerTexts.description}</p>
          {form}
        </div>
      </section>
    );
  }

  return (
    <dialog
      ref={dialogRef}
      className="discount-dialog"
      aria-labelledby={titleId}
      aria-describedby={descriptionId}
      onKeyDown={trapDialogFocus}
      onCancel={(event) => {
        event.preventDefault();
        closeDialog();
      }}
      onClose={() => {
        releaseSurfaceLock();
        setIsOpen(false);
      }}
    >
      <div className="discount-dialog__header">
        <div>
          <p className="eyebrow">Primeira compra</p>
          <h2 id={titleId}>{offerTexts.title}</h2>
        </div>
        <button
          ref={closeButtonRef}
          className="discount-dialog__close"
          type="button"
          aria-label={content.close}
          onClick={closeDialog}
        >
          ×
        </button>
      </div>
      <p id={descriptionId}>{offerTexts.description}</p>
      {isOpen ? form : null}
    </dialog>
  );
}
