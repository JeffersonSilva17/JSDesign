import 'server-only';

import { getApiInternalUrl } from '@/bff/apiClient';
import { fetchSitemapJson } from '@/bff/catalogSitemapTransport';
import { sitemapCategories, sitemapPage } from '@/bff/catalogSitemapValidation';

export async function fetchSitemapPage(page: number, clientToken: string, productChild = false) {
  return sitemapPage(await fetchSitemapJson(new URL(`/api/v1/catalog/sitemap?page=${page}`, getApiInternalUrl()), 256 * 1024, { productChild, clientToken }), page);
}

export async function fetchSitemapCategories(clientToken: string) {
  return sitemapCategories(await fetchSitemapJson(new URL('/api/v1/catalog/sitemap-facets', getApiInternalUrl()), 4 * 1024 * 1024, { clientToken }));
}
