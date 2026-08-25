import Link from 'next/link';

import type { CatalogFacets, CatalogFilters } from '@/bff/catalogApi';
import { catalogHref } from '@/bff/catalogApi';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

type Props = Readonly<{ facets: CatalogFacets; filters: CatalogFilters }>;

export function CatalogFiltersView({ facets, filters }: Props) {
  const groups = [
    { label: catalogContent.filters.category, key: 'category' as const, values: facets.categories.map((item) => ({ value: item.slug, label: item.label })) },
    { label: catalogContent.filters.occasion, key: 'occasion' as const, values: facets.occasions.map((item) => ({ value: item.key, label: item.label })) },
    { label: catalogContent.filters.modality, key: 'modality' as const, values: facets.modalities },
  ];
  return (
    <nav className="catalog-filters" aria-label={catalogContent.filters.title}>
      {groups.map((group) => (
        <div className="catalog-filter-group" key={group.key}>
          <strong>{group.label}</strong>
          <div className="catalog-filter-links">
            {group.values.map((item) => {
              const selected = filters[group.key] === item.value;
              return <Link aria-current={selected ? 'page' : undefined} key={item.value} href={catalogHref({ ...filters, [group.key]: selected ? undefined : item.value, page: 1 } as CatalogFilters)}>{item.label}</Link>;
            })}
          </div>
        </div>
      ))}
      {(filters.category || filters.occasion || filters.modality) && <Link className="catalog-filter-clear" href="/produtos">{catalogContent.filters.clear}</Link>}
    </nav>
  );
}
