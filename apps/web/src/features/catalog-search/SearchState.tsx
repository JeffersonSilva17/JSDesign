import Link from 'next/link';

import { searchContent } from '@/features/public-store/publicLayoutContent';

type StateKind = 'empty' | 'invalid' | 'unavailable' | 'page-out-of-range';

export function SearchState({ kind, actionHref = '/buscar', term }: Readonly<{ kind: StateKind; actionHref?: string; term?: string }>) {
  const content = kind === 'invalid'
    ? [searchContent.states.invalidTitle, searchContent.states.invalidDescription, searchContent.states.retry]
    : kind === 'unavailable'
      ? [searchContent.states.unavailableTitle, searchContent.states.unavailableDescription, searchContent.states.retry]
      : kind === 'page-out-of-range'
        ? [searchContent.states.outOfRangeTitle, searchContent.states.outOfRangeDescription, searchContent.states.lastPage]
        : [searchContent.states.emptyTitle, searchContent.states.emptyDescription, searchContent.states.retry];
  return (
    <section className="catalog-state" aria-labelledby={`catalog-search-state-${kind}`}>
      <h2 id={`catalog-search-state-${kind}`}>{content[0]}</h2>
      {term && <p>{searchContent.termLabel}: <strong>{term}</strong></p>}
      <p>{content[1]}</p>
      <Link className="button button--secondary" href={actionHref}>{content[2]}</Link>
    </section>
  );
}
