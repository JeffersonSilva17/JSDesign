import type { CatalogFilters, CatalogListing as CatalogListingData } from '@/bff/catalogApi';
import { catalogHref } from '@/bff/catalogApi';
import { CatalogCard } from '@/features/catalog/CatalogCard';
import { CatalogPagination } from '@/features/catalog/CatalogPagination';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

export function CatalogListing({ listing, filters }: Readonly<{ listing: CatalogListingData; filters: CatalogFilters }>) {
  if (listing.data.length === 0) {
    if (listing.meta.total > 0) {
      return <CatalogState kind="page-out-of-range" actionHref={catalogHref(filters, { page: listing.meta.last_page })} actionLabel={catalogContent.states.backToLastPage} />;
    }

    return <CatalogState kind="empty" actionHref={catalogHref(filters, { page: 1 })} />;
  }

  const returnHref = catalogHref(filters);
  return (
    <>
      <div className="catalog-grid">{listing.data.map((product, index) => <CatalogCard key={product.id} product={product} preload={index === 0} returnHref={returnHref} />)}</div>
      <CatalogPagination filters={filters} current={listing.meta.current_page} last={listing.meta.last_page} />
    </>
  );
}
