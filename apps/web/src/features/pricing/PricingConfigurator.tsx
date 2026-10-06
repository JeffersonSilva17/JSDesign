'use client';

import { useEffect, useRef, useState } from 'react';
import Link from 'next/link';

import type { CatalogModality } from '@/bff/catalogApi';
import { isPublicError, isQuote } from '@/bff/pricingValidation';
import type { PricingError, PricingQuote } from '@/bff/pricingValidation';
import { pricingConfiguratorContent } from '@/i18n/pricingConfiguratorContent';
import type { SupportedLocale } from '@/i18n/locales';

type Props = Readonly<{ slug: string; modelKey: string | null; minimumQuantity: number | null; modality: CatalogModality; ctaHref: string; locale: SupportedLocale }>;
type State = Readonly<{ status: 'idle' | 'loading' | 'quote' | 'error'; quote?: PricingQuote; error?: PricingError }>;

export function PricingConfigurator({ slug, modelKey, minimumQuantity, modality, ctaHref, locale }: Props) {
  const text = pricingConfiguratorContent[locale];
  const minimum = modality === 'digital_ready' ? 1 : Math.max(1, minimumQuantity ?? 1);
  const [quantity, setQuantity] = useState(String(minimum));
  const [state, setState] = useState<State>({ status: 'idle' });
  const [maximumQuantity, setMaximumQuantity] = useState<number | null>(null);
  const [retry, setRetry] = useState(0);
  const sequence = useRef(0);
  const current = useRef<AbortController | null>(null);
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
        }
        else setState({ status: 'error', error: isPublicError(payload) ? payload : undefined });
      } catch {
        if (id === sequence.current && !controller.signal.aborted) setState({ status: 'error' });
      }
    }, 250);
    return () => { window.clearTimeout(timer); controller.abort(); };
  }, [slug, modelKey, quantity, parsed, valid, locale, retry]);

  const quote = state.quote;
  return <section className={`summary-panel ${modality === 'physical_personalized' ? 'configurator-physical' : ''} pricing-panel`} aria-labelledby="pricing-summary-title">
    <h2 id="pricing-summary-title">{text.quote}</h2>
    <label htmlFor="pricing-quantity">{text.quantity}</label>
    <input id="pricing-quantity" name="quantity" type="number" inputMode="numeric" step="1" min={minimum}
      max={maximum} value={quantity} aria-describedby="pricing-limits pricing-message"
      onChange={(event) => { setQuantity(event.currentTarget.value); setState({ status: 'idle' }); }} />
    <p id="pricing-limits">{text.min}: {minimum}{maximumQuantity !== null ? ` · ${text.max}: ${maximum}` : ''}</p>
    <dl className="pricing-panel__values" aria-live="polite" aria-atomic="true">
      {quote && <><div><dt>{text.unit}</dt><dd>{formatMinor(quote.unit_price_minor, locale)}</dd></div>
        <div><dt>{text.subtotal}</dt><dd>{formatMinor(quote.subtotal_minor, locale)}</dd></div>
        <div><dt>{text.discount}</dt><dd>{formatMinor(quote.discount_minor, locale)}</dd></div>
        <div><dt>{text.total}</dt><dd><strong>{formatMinor(quote.total_minor, locale)}</strong></dd></div>
        <div><dt>{text.currency}</dt><dd>{quote.currency}</dd></div></>}
    </dl>
    <p id="pricing-message" aria-live="polite" role="status">
      {state.status === 'loading' ? text.pending : state.status === 'error' ? (state.error?.error.message ?? text.unavailable) : !valid ? text.invalid : ''}
    </p>
    {state.status === 'error' && <button type="button" className="button button--secondary" onClick={() => setRetry((value) => value + 1)}>{text.retry}</button>}
    <Link className="button button--primary" href={ctaHref}>{text.cta}</Link>
    <p>{text.handoff}</p>
  </section>;
}

function formatMinor(minor: number, locale: SupportedLocale): string {
  const amount = BigInt(minor);
  const cents = (amount % 100n).toString().padStart(2, '0');
  return new Intl.NumberFormat(locale, { style: 'currency', currency: 'EUR' })
    .formatToParts(amount / 100n)
    .map((part) => part.type === 'fraction' ? cents : part.value)
    .join('');
}
