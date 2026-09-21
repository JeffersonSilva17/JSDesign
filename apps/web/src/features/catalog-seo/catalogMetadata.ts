import 'server-only';

import type { Metadata } from 'next';

import type { CatalogFilters, CatalogImage, CatalogListing } from '@/bff/catalogApi';
import { catalogContent, publicContent } from '@/features/public-store/publicLayoutContent';
import { catalogSeoContent } from '@/i18n/catalogSeoContent';

import { listingPolicy } from './catalogPolicy';
import { publicUrl, seoConfig } from './siteUrl';
import { isSocialImage } from './socialImage';

export function socialImage(image?: CatalogImage | null) {
  return image && isSocialImage(image.url)
    ? { url: publicUrl(image.url), alt: image.alt_text }
    : { url: publicUrl('/catalog-social.png'), alt: catalogSeoContent.socialAlt, width: 1200, height: 630 };
}

export function catalogMetadata(title: string, description: string, path?: string, index = false, image?: CatalogImage | null): Metadata {
  const enabled = seoConfig().indexing;
  const canonical = enabled && path ? publicUrl(path) : undefined;
  const images = [socialImage(image)];
  return {
    title, description, robots: { index: enabled && index, follow: enabled },
    alternates: canonical ? { canonical } : undefined,
    openGraph: { title, description, type: 'website', locale: 'pt_BR', siteName: publicContent.brand.name, ...(canonical ? { url: canonical } : {}), images },
    twitter: { card: 'summary_large_image', title, description, images },
  };
}

export function listingPresentation(filters: CatalogFilters, listing: CatalogListing | null) {
  const policy = listingPolicy(filters, listing, seoConfig().indexing);
  const label = listing && listing.data.length && filters.page <= listing.meta.last_page && !filters.occasion && !filters.modality ? listing.meta.filter_labels.category : undefined;
  const title = label ?? catalogContent.listing.title;
  const description = label ? catalogSeoContent.categoryDescription(label) : catalogContent.metadata.products.description;
  return { title, description, policy, metadata: catalogMetadata(catalogSeoContent.pageTitle(label ?? catalogContent.metadata.products.title, filters.page), description, policy.canonical, policy.index) };
}
