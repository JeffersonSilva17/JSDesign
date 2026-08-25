import Link from 'next/link';

import { catalogContent } from '@/features/public-store/publicLayoutContent';

type Props = Readonly<{
  kind: 'invalid' | 'unavailable' | 'empty' | 'page-out-of-range';
  actionHref?: string;
  actionLabel?: string;
}>;

export function CatalogState({ kind, actionHref = '/produtos', actionLabel = catalogContent.states.back }: Props) {
  const title = kind === 'invalid' ? catalogContent.states.invalidTitle : kind === 'unavailable' ? catalogContent.states.unavailableTitle : kind === 'page-out-of-range' ? catalogContent.states.pageOutOfRangeTitle : catalogContent.listing.emptyTitle;
  const description = kind === 'invalid' ? catalogContent.states.invalidDescription : kind === 'unavailable' ? catalogContent.states.unavailableDescription : kind === 'page-out-of-range' ? catalogContent.states.pageOutOfRangeDescription : catalogContent.listing.emptyDescription;
  return (
    <section className="catalog-state" aria-labelledby={`catalog-state-${kind}`}>
      <h2 id={`catalog-state-${kind}`}>{title}</h2>
      <p>{description}</p>
      <Link className="button button--secondary" href={actionHref}>{actionLabel}</Link>
    </section>
  );
}
