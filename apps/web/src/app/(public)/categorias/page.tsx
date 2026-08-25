import type { Metadata } from 'next';
import Link from 'next/link';

import { fetchCatalogFacets } from '@/bff/catalogApi';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = catalogContent.metadata.categories;

export default async function CategoriasPage() {
  let facets;
  try {
    facets = await fetchCatalogFacets();
  } catch { facets = null; }
  if (facets === null) return <CatalogState kind="unavailable" />;
  return <div className="catalog-page">
    <header className="catalog-page__header"><p className="eyebrow">{catalogContent.categories.eyebrow}</p><h1>{catalogContent.categories.title}</h1><p>{catalogContent.categories.description}</p></header>
    {facets.categories.length === 0 && facets.occasions.length === 0 && facets.modalities.length === 0 ? <p>{catalogContent.categories.empty}</p> : <section className="facet-index" aria-label={catalogContent.categories.title}>
      {facets.categories.map((category) => <Link className="facet-index__item" href={`/produtos?category=${encodeURIComponent(category.slug)}`} key={`category-${category.slug}`}>{category.label}</Link>)}
      {facets.occasions.map((occasion) => <Link className="facet-index__item" href={`/produtos?occasion=${encodeURIComponent(occasion.key)}`} key={`occasion-${occasion.key}`}>{occasion.label}</Link>)}
      {facets.modalities.map((modality) => <Link className="facet-index__item" href={`/produtos?modality=${encodeURIComponent(modality.value)}`} key={`modality-${modality.value}`}>{modality.label}</Link>)}
    </section>}
  </div>;
}
