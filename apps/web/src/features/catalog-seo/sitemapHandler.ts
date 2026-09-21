import 'server-only';

import { fetchSitemapCategories, fetchSitemapPage } from '@/bff/catalogSitemapApi';
import { SitemapError } from '@/bff/catalogSitemapTransport';

import { seoConfig } from './siteUrl';
import { editorialXml, sitemapXml } from './sitemapXml';
import { clientHeader, validClient } from './sitemapIdentity';

const headers = { 'Cache-Control': 'no-store', 'X-Robots-Tag': 'noindex' };
type Document = 'index' | 'editorial' | 'products';

export async function sitemapResponse(request: Request, document: Document): Promise<Response> {
  const { indexing, origin } = seoConfig();
  try {
    if (!indexing) throw new SitemapError(404);
    const query = new URL(request.url).search;
    let page = 1;
    if (document === 'products') {
      if (!/^\?page=[1-9][0-9]{0,4}$/.test(query) || Number(query.slice(6)) > 10000) throw new SitemapError(422);
      page = Number(query.slice(6));
    } else if (query !== '') throw new SitemapError(422);
    const clientToken = request.headers.get(clientHeader) ?? '';
    if (!validClient(clientToken)) throw new SitemapError();
    let xml: string;
    if (document === 'products') {
      const products = await fetchSitemapPage(page, clientToken, true);
      xml = sitemapXml(products.data.map(({ slug }) => `/produtos/${slug}`), origin);
    } else {
      const editorial = editorialXml(await fetchSitemapCategories(clientToken), origin);
      if (document === 'editorial') xml = editorial;
      else {
        const products = await fetchSitemapPage(1, clientToken);
        xml = sitemapXml(['/sitemap-editorial.xml', ...Array.from({ length: products.meta.last_page }, (_, index) => `/sitemap-catalogo.xml?page=${index + 1}`)], origin, true);
      }
    }
    return new Response(xml, { headers: { ...headers, 'Content-Type': 'application/xml; charset=utf-8' } });
  } catch (error) {
    const failure = error instanceof SitemapError ? error : new SitemapError();
    const message = failure.status === 422 ? 'Parâmetro de sitemap inválido.' : failure.status === 404 ? 'Sitemap não encontrado.' : 'Sitemap temporariamente indisponível.';
    return Response.json({ message }, { status: failure.status, headers: { ...headers, ...([429, 503].includes(failure.status) ? { 'Retry-After': failure.retryAfter } : {}) } });
  }
}
