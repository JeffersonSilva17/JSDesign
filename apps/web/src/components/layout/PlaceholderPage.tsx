import Link from 'next/link';

import { placeholderPages, publicCta, type PlaceholderPageKey } from '@/features/public-store/publicLayoutContent';

type PlaceholderPageProps = Readonly<{
  pageKey: PlaceholderPageKey;
}>;

export function PlaceholderPage({ pageKey }: PlaceholderPageProps) {
  const page = placeholderPages[pageKey];
  const fallbackCta =
    pageKey === 'produtos'
      ? { href: '/', label: 'Voltar para a home' }
      : { href: publicCta.href, label: 'Voltar para produtos' };

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
