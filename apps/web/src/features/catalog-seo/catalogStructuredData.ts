import type { CatalogListing, CatalogProduct } from '../../bff/catalogApi';

export function productStructuredData(product: CatalogProduct, origin: string) {
  return {
    '@context': 'https://schema.org', '@type': 'Product', name: product.name,
    description: product.description, url: new URL(`/produtos/${product.slug}`, origin).href,
    ...(product.primary_image ? { image: new URL(product.primary_image.url, origin).href } : {}),
  };
}

export function collectionStructuredData(listing: CatalogListing, path: string, origin: string) {
  return {
    '@context': 'https://schema.org', '@type': 'CollectionPage', url: new URL(path, origin).href,
    mainEntity: {
      '@type': 'ItemList', itemListElement: listing.data.map((item, index) => ({
        '@type': 'ListItem', position: (listing.meta.current_page - 1) * listing.meta.per_page + index + 1,
        name: item.name, url: new URL(`/produtos/${item.slug}`, origin).href,
      })),
    },
  };
}

export function serializeJsonLd(value: ReturnType<typeof productStructuredData> | ReturnType<typeof collectionStructuredData>): string {
  return JSON.stringify(value).replace(/</g, '\\u003c');
}
