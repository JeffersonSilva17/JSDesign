import Link from 'next/link';

import type { CatalogSearchCriteria, CatalogSearchResult } from '@/bff/catalogApi';
import { publicSearchHref } from '@/bff/catalogApi';
import { CatalogCard } from '@/features/catalog/CatalogCard';
import { SearchState } from '@/features/catalog-search/SearchState';
import { SearchSuggestions } from '@/features/catalog-search/SearchSuggestions';
import { searchContent } from '@/features/public-store/publicLayoutContent';

export function SearchResults({ result, criteria }: Readonly<{ result: CatalogSearchResult; criteria: CatalogSearchCriteria }>) {
  const returnHref = publicSearchHref(criteria);
  if (result.meta.current_page > result.meta.last_page) {
    return <SearchState kind="page-out-of-range" actionHref={publicSearchHref(criteria, result.meta.last_page)} />;
  }

  const invitation = result.data.intent.type === 'invitation' && result.data.intent.handoff_href !== null;
  return (
    <div className="catalog-search-results" aria-live="polite">
      {result.meta.total === 0 && <SearchState kind="empty" actionHref="/buscar" term={result.meta.query} />}
      {invitation && (
        <aside className="catalog-search-invitation" aria-labelledby="catalog-search-invitation-title">
          <h2 id="catalog-search-invitation-title">{searchContent.invitation.title}</h2>
          <p>{searchContent.invitation.description}</p>
          <Link className="button button--secondary" href={result.data.intent.handoff_href ?? '/produtos'}>{searchContent.invitation.action}</Link>
        </aside>
      )}
      {result.data.exact_groups.length > 0 && (
        <section className="catalog-search-section" aria-labelledby="catalog-search-exact-title">
          <h2 id="catalog-search-exact-title">{searchContent.exact.title}</h2>
          {result.data.exact_groups.map((group, groupIndex) => (
            <section className="catalog-search-group" key={group.category.slug} aria-labelledby={`catalog-search-group-${group.category.slug}`}>
              <h3 id={`catalog-search-group-${group.category.slug}`}>{searchContent.exact.categoryPrefix}: {group.category.label}</h3>
              <div className="catalog-grid">
                {group.items.map((product, index) => <CatalogCard key={product.id} product={product} returnHref={returnHref} headingLevel={4} preload={groupIndex === 0 && index === 0} />)}
              </div>
            </section>
          ))}
        </section>
      )}
      {result.data.similar.length > 0 && (
        <section className="catalog-search-section" aria-labelledby="catalog-search-similar-title">
          <h2 id="catalog-search-similar-title">{searchContent.similar.title}</h2>
          <p>{searchContent.similar.description}</p>
          <div className="catalog-grid">
            {result.data.similar.map((product, index) => <CatalogCard key={product.id} product={product} returnHref={returnHref} headingLevel={3} preload={result.data.exact_groups.length === 0 && index === 0} />)}
          </div>
        </section>
      )}
      <SearchSuggestions suggestions={result.data.suggestions} />
      <SearchPagination criteria={criteria} current={result.meta.current_page} last={result.meta.last_page} />
    </div>
  );
}

function SearchPagination({ criteria, current, last }: Readonly<{ criteria: CatalogSearchCriteria; current: number; last: number }>) {
  if (last <= 1 || current > last) return null;
  return (
    <nav className="catalog-pagination" aria-label={searchContent.pagination.label}>
      {current > 1 && <Link href={publicSearchHref(criteria, current - 1)}>{searchContent.pagination.previous}</Link>}
      {paginationWindow(current, last).map((page, index) => page === 'ellipsis'
        ? <span key={`ellipsis-${index}`} aria-hidden="true">…</span>
        : <Link key={page} href={publicSearchHref(criteria, page)} aria-current={page === current ? 'page' : undefined}>{page}</Link>)}
      {current < last && <Link href={publicSearchHref(criteria, current + 1)}>{searchContent.pagination.next}</Link>}
    </nav>
  );
}

function paginationWindow(current: number, last: number): Array<number | 'ellipsis'> {
  const visible = new Set([1, last]);
  for (let page = Math.max(1, current - 1); page <= Math.min(last, current + 1); page += 1) visible.add(page);
  const pages = [...visible].sort((left, right) => left - right);
  const output: Array<number | 'ellipsis'> = [];
  for (const page of pages) {
    const previous = output.at(-1);
    if (typeof previous === 'number' && page - previous > 1) output.push('ellipsis');
    output.push(page);
  }
  return output;
}
