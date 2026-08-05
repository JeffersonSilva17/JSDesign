import Link from 'next/link';

import {
  placeholderFallbackCtas,
  placeholderPages,
  type PlaceholderPageKey,
} from '@/features/public-store/publicLayoutContent';

type PlaceholderPageProps = Readonly<{
  pageKey: PlaceholderPageKey;
}>;

export function PlaceholderPage({ pageKey }: PlaceholderPageProps) {
  const page = placeholderPages[pageKey];
  const fallbackCta = pageKey === 'produtos' ? placeholderFallbackCtas.produtos : placeholderFallbackCtas.default;

  return (
    <section className="placeholder-page" aria-labelledby="placeholder-title">
      <p className="eyebrow">{page.eyebrow}</p>
      <h1 id="placeholder-title">{page.title}</h1>
      <p>{page.description}</p>
      <Link className="button button--secondary" href={fallbackCta.href}>
        {fallbackCta.label}
      </Link>
    </section>
  );
}
