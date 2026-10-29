'use client';

import { useEffect, useRef, useState, type FormEvent } from 'react';
import { useRouter } from 'next/navigation';

import type { CatalogModality, CatalogTaxonomy } from '@/bff/catalogApi';
import { isPublicError, isQuote } from '@/bff/pricingValidation';
import type { PricingError, PricingQuote } from '@/bff/pricingValidation';
import { isCartError, isCartItem } from '@/bff/cartValidation';
import { cartConfiguratorContent } from '@/i18n/cartConfiguratorContent';
import { pricingConfiguratorContent } from '@/i18n/pricingConfiguratorContent';
import type { SupportedLocale } from '@/i18n/locales';

type Props = Readonly<{ slug: string; modelKey: string | null; minimumQuantity: number | null; modality: CatalogModality; locale: SupportedLocale; themes: readonly CatalogTaxonomy[]; allowsDeferredBriefing: boolean; available: boolean }>;
type State = Readonly<{ status: 'idle' | 'loading' | 'quote' | 'error'; quote?: PricingQuote; error?: PricingError }>;
type FormFields = Readonly<{ theme: string; personalizationText: string; colors: string; eventName: string; eventDate: string; eventTime: string; eventLocation: string; note: string; thumbnail: 'none'|'client_file'|'create_by_js_designs'; defer: boolean }>;
const emptyFields: FormFields = { theme:'', personalizationText:'', colors:'', eventName:'', eventDate:'', eventTime:'', eventLocation:'', note:'', thumbnail:'none', defer:false };

export function PricingConfigurator({ slug, modelKey, minimumQuantity, modality, locale: initialLocale, themes, allowsDeferredBriefing, available }: Props) {
  const router = useRouter();
  const localeText = cartConfiguratorContent;
  const [locale, setLocale] = useState<SupportedLocale>(initialLocale);
  const text = pricingConfiguratorContent[locale];
  const cartText = localeText[locale];
  const minimum = modality === 'digital_ready' ? 1 : Math.max(1, minimumQuantity ?? 1);
  const [quantity, setQuantity] = useState(String(minimum));
  const [state, setState] = useState<State>({ status: 'idle' });
  const [maximumQuantity, setMaximumQuantity] = useState<number | null>(null);
  const [retry, setRetry] = useState(0);
  const [fields, setFields] = useState<FormFields>(emptyFields);
  const [fieldErrors, setFieldErrors] = useState<Partial<Record<keyof FormFields, string>>>({});
  const [submitState, setSubmitState] = useState<'idle'|'submitting'|'error'>('idle');
  const [submitMessage, setSubmitMessage] = useState('');
  const sequence = useRef(0);
  const current = useRef<AbortController | null>(null);
  const idempotency = useRef<{ body: string; key: string; csrfToken: string } | null>(null);
  const parsed = /^(?:0|[1-9]\d*)$/.test(quantity) ? Number(quantity) : NaN;
  const maximum = maximumQuantity ?? 10000;
  const valid = Number.isSafeInteger(parsed) && parsed >= minimum && parsed <= maximum && (modality !== 'digital_ready' || parsed === 1);

  useEffect(() => {
    const id = ++sequence.current;
    current.current?.abort();
    if (!valid) return;
    const controller = new AbortController();
    current.current = controller;
    const timer = window.setTimeout(async () => {
      try {
        setState({ status: 'loading' });
        const response = await fetch('/api/pricing/quotes', {
          method: 'POST', headers: { 'content-type': 'application/json', accept: 'application/json', 'accept-language': locale },
          cache: 'no-store', signal: controller.signal,
          body: JSON.stringify({ product_slug: slug, ...(modelKey === null ? {} : { model_key: modelKey }), quantity: parsed, currency: 'EUR' }),
        });
        const payload: unknown = await response.json();
        if (id !== sequence.current || controller.signal.aborted) return;
        if (response.ok && isQuote(payload) && payload.product_slug === slug && payload.model_key === modelKey && payload.quantity === parsed) {
          setMaximumQuantity(payload.maximum_quantity);
          setState({ status: 'quote', quote: payload });
        } else setState({ status: 'error', error: isPublicError(payload) ? payload : undefined });
      } catch { if (id === sequence.current && !controller.signal.aborted) setState({ status: 'error' }); }
    }, 250);
    return () => { window.clearTimeout(timer); controller.abort(); };
  }, [slug, modelKey, quantity, parsed, valid, locale, retry]);

  const setField = <K extends keyof FormFields>(name: K, value: FormFields[K]) => {
    setFields((currentFields) => ({ ...currentFields, [name]: value }));
    setFieldErrors((currentErrors) => ({ ...currentErrors, [name]: undefined }));
    setSubmitMessage('');
  };
  useEffect(() => { document.cookie = `jsd_locale=${locale}; Path=/; Max-Age=2592000; SameSite=Strict; Secure`; }, [locale]);

  async function submit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    if (modality === 'digital_ready') { setSubmitState('error'); setSubmitMessage(cartText.unsupported); return; }
    const required: (keyof FormFields)[] = modality === 'physical_personalized'
      ? ['theme','personalizationText','eventDate']
      : ['theme','colors','eventName','eventDate','eventTime','eventLocation'];
    const errors: Partial<Record<keyof FormFields, string>> = {};
    for (const key of required) if (typeof fields[key] !== 'string' || !String(fields[key]).trim()) errors[key] = cartText.required;
    if (!valid || !state.quote) { setSubmitState('error'); setSubmitMessage(text.invalid); return; }
    setFieldErrors(errors);
    if (Object.keys(errors).length) { setSubmitState('error'); setSubmitMessage(cartText.invalid); return; }
    const configuration: Record<string,string|boolean> = modality === 'physical_personalized'
      ? { configuration_type: modality, theme: fields.theme.trim(), personalization_text: fields.personalizationText.trim(), event_date: fields.eventDate, ...(fields.note.trim() ? { note: fields.note.trim() } : {}), thumbnail_choice: fields.thumbnail, defer_briefing: allowsDeferredBriefing && fields.defer }
      : { configuration_type: modality, invite_type: modelKey === 'complex' ? 'complex' : 'standard', theme: fields.theme.trim(), colors: fields.colors.trim(), event_name: fields.eventName.trim(), event_date: fields.eventDate, event_time: fields.eventTime, event_location: fields.eventLocation.trim(), thumbnail_choice: fields.thumbnail, defer_briefing: allowsDeferredBriefing && fields.defer };
    const payload = { product_slug: slug, model_key: modelKey ?? 'padrao', quantity: parsed, quote_snapshot_id: state.quote.quote_snapshot_id, configuration };
    const body = JSON.stringify(payload);
    setSubmitState('submitting'); setSubmitMessage('');
    try {
      if (!idempotency.current || idempotency.current.body !== body) {
        const sessionResponse = await fetch('/api/cart/session', { cache: 'no-store', credentials: 'same-origin' });
        const session: unknown = await sessionResponse.json();
        if (!sessionResponse.ok || !isRecord(session) || typeof session.csrf_token !== 'string' || typeof session.idempotency_key !== 'string' || !/^[A-Za-z0-9_-]{43}$/.test(session.csrf_token) || !/^[A-Za-z0-9_-]{43}$/.test(session.idempotency_key)) throw new Error('session');
        idempotency.current = { body, key: session.idempotency_key, csrfToken: session.csrf_token };
      }
      const response = await fetch('/api/cart/items', {
        method: 'POST', credentials: 'same-origin', cache: 'no-store',
        headers: { 'content-type':'application/json', accept:'application/json', 'accept-language':locale, 'x-csrf-token':idempotency.current.csrfToken, 'idempotency-key':idempotency.current.key },
        body,
      });
      const result: unknown = await response.json();
      if ((response.status === 200 || response.status === 201) && isCartItem(result)) {
        setSubmitState('idle'); router.push('/carrinho?incluido=1'); return;
      }
      if (isCartError(result)) {
        setSubmitState('error'); setSubmitMessage(result.error.message);
        if (result.error.code === 'stale_quote') setRetry((value) => value + 1);
      } else { setSubmitState('error'); setSubmitMessage(cartText.server); }
    } catch { setSubmitState('error'); setSubmitMessage(cartText.server); }
  }

  const field = (key: keyof FormFields, label: string, maxLength: number, type = 'text') => <div className="cart-configurator__field" key={key}>
    <label htmlFor={`cart-${key}`}>{label}</label>
    <input id={`cart-${key}`} name={key} type={type} maxLength={maxLength} list={key === 'theme' ? 'cart-theme-options' : undefined} value={String(fields[key])} required={key !== 'note'} aria-invalid={Boolean(fieldErrors[key])} aria-describedby={fieldErrors[key] ? `cart-${key}-error` : undefined} onChange={(event) => setField(key, event.currentTarget.value)} />
    {key === 'theme' && <><datalist id="cart-theme-options">{themes.filter((item) => item.type === 'theme').map((theme) => <option key={theme.key} value={theme.label} />)}</datalist><small>{cartText.themeHint}</small></>}
    {fieldErrors[key] && <small id={`cart-${key}-error`} role="alert">{fieldErrors[key]}</small>}
  </div>;

  return <section className={`summary-panel pricing-panel ${modality === 'physical_personalized' ? 'configurator-physical' : ''}`} aria-labelledby="pricing-summary-title">
    <h2 id="pricing-summary-title">{text.quote}</h2>
    <label htmlFor="pricing-quantity">{cartText.quantity}</label>
    <input id="pricing-quantity" name="quantity" type="number" inputMode="numeric" step="1" min={minimum} max={maximum} value={quantity} aria-describedby="pricing-limits pricing-message" onChange={(event) => { setQuantity(event.currentTarget.value); setState({ status: 'idle' }); }} />
    <p id="pricing-limits">{text.min}: {minimum}{maximumQuantity !== null ? ` · ${text.max}: ${maximum}` : ''}</p>
    <dl className="pricing-panel__values" aria-live="polite" aria-atomic="true">
      {state.quote && <><div><dt>{text.unit}</dt><dd>{formatMinor(state.quote.unit_price_minor, locale)}</dd></div><div><dt>{text.subtotal}</dt><dd>{formatMinor(state.quote.subtotal_minor, locale)}</dd></div><div><dt>{text.discount}</dt><dd>{formatMinor(state.quote.discount_minor, locale)}</dd></div><div><dt>{text.total}</dt><dd><strong>{formatMinor(state.quote.total_minor, locale)}</strong></dd></div><div><dt>{text.currency}</dt><dd>{state.quote.currency}</dd></div></>}
    </dl>
    <p id="pricing-message" aria-live="polite" role="status">{state.status === 'loading' ? text.pending : state.status === 'error' ? (state.error?.error.message ?? text.unavailable) : !valid ? text.invalid : ''}</p>
    {state.status === 'error' && <button type="button" className="button button--secondary" onClick={() => setRetry((value) => value + 1)}>{text.retry}</button>}
    <form onSubmit={submit} noValidate>
      {modality !== 'digital_ready' && <fieldset className="cart-configurator"><legend>{cartText.summary}</legend>
        {field('theme', cartText.theme, 120)}
        {modality === 'physical_personalized' ? <>{field('personalizationText', cartText.text, 120)}{field('eventDate', cartText.date, 10, 'date')}{field('note', cartText.note, 500)}</>
          : <>{field('colors', cartText.colors, 160)}{field('eventName', cartText.eventName, 120)}{field('eventDate', cartText.date, 10, 'date')}{field('eventTime', cartText.time, 5, 'time')}{field('eventLocation', cartText.location, 200)}</>}
        <fieldset><legend>{cartText.thumbnail}</legend>{(['none','client_file','create_by_js_designs'] as const).map((choice) => <label key={choice}><input type="radio" name="thumbnail" value={choice} checked={fields.thumbnail === choice} onChange={() => setField('thumbnail', choice)} />{choice === 'none' ? cartText.none : choice === 'client_file' ? cartText.clientFile : cartText.create}</label>)}</fieldset>
        {allowsDeferredBriefing && <><label><input type="checkbox" checked={fields.defer} onChange={(event) => setField('defer', event.currentTarget.checked)} />{cartText.deferred}</label><p>{cartText.deferredHint}</p></>}
      </fieldset>}
      <label htmlFor="cart-locale">{cartText.locale}</label><select id="cart-locale" value={locale} onChange={(event) => setLocale(event.currentTarget.value as SupportedLocale)}><option value="pt-BR">Português</option><option value="en">English</option><option value="es">Español</option></select>
      {submitMessage && <p role="alert" aria-live="assertive">{submitMessage}</p>}
      {modality === 'digital_ready' ? <p>{cartText.unsupported}</p> : <div className="cart-configurator__actions"><button className="button button--primary" type="submit" disabled={!available || submitState === 'submitting' || state.status !== 'quote'}>{submitState === 'submitting' ? cartText.submit : cartText.add}</button><button className="button button--secondary" type="submit" disabled={!available || submitState === 'submitting' || state.status !== 'quote'}>{cartText.buy}</button></div>}
    </form>
  </section>;
}

function isRecord(value: unknown): value is Record<string,unknown> { return value !== null && typeof value === 'object' && !Array.isArray(value); }
function formatMinor(minor: number, locale: SupportedLocale): string {
  const amount = BigInt(minor); const cents = (amount % 100n).toString().padStart(2, '0');
  return new Intl.NumberFormat(locale, { style: 'currency', currency: 'EUR' }).formatToParts(amount / 100n).map((part) => part.type === 'fraction' ? cents : part.value).join('');
}
