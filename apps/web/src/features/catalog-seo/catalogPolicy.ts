import type { CatalogFilters, CatalogListing } from '../../bff/catalogApi';
import { catalogHref } from '../../bff/catalogParams.ts';

export function listingPolicy(filters: CatalogFilters, listing: CatalogListing | null, enabled: boolean) {
  const valid = listing !== null && filters.page <= listing.meta.last_page &&
    (!filters.category || (listing.data.length > 0 && !!listing.meta.filter_labels.category));
  return {
    index: enabled && valid && !filters.occasion && !filters.modality,
    follow: enabled,
    canonical: enabled && valid ? catalogHref(filters) : undefined,
  };
}
