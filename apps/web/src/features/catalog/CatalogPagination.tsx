import Link from 'next/link';

import type { CatalogFilters } from '@/bff/catalogApi';
import { catalogHref } from '@/bff/catalogApi';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

export function CatalogPagination({ filters, current, last }: Readonly<{ filters: CatalogFilters; current: number; last: number }>) {
  if (last <= 1) return null;
  const pages = paginationWindow(current, last);

  return (
    <nav className="catalog-pagination" aria-label={catalogContent.pagination.label}>
      {current > 1 && <Link href={catalogHref(filters, { page: current - 1 })}>{catalogContent.pagination.previous}</Link>}
      {pages.map((page, index) => page === 'ellipsis'
        ? <span aria-hidden="true" key={`ellipsis-${index}`}>…</span>
        : <Link aria-current={page === current ? 'page' : undefined} href={catalogHref(filters, { page })} key={page}>{page}</Link>)}
      {current < last && <Link href={catalogHref(filters, { page: current + 1 })}>{catalogContent.pagination.next}</Link>}
    </nav>
  );
}

function paginationWindow(current: number, last: number): Array<number | 'ellipsis'> {
  const visible = new Set([1, last]);
  for (let page = Math.max(1, current - 1); page <= Math.min(last, current + 1); page++) {
    visible.add(page);
  }

  const pages = [...visible].sort((left, right) => left - right);
  const windowed: Array<number | 'ellipsis'> = [];
  for (const page of pages) {
    const previous = windowed.at(-1);
    if (typeof previous === 'number' && page - previous > 1) {
      windowed.push('ellipsis');
    }
    windowed.push(page);
  }

  return windowed;
}
