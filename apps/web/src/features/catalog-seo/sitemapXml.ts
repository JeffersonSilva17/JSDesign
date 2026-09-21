import { SitemapError } from '../../bff/catalogSitemapTransport.ts';
import type { SitemapCategories } from '../../bff/catalogSitemapValidation';

export function sitemapXml(paths: readonly string[], origin: string, index = false): string {
  const name = index ? 'sitemapindex' : 'urlset';
  const entry = index ? 'sitemap' : 'url';
  if (paths.length > 50000) throw new SitemapError();
  const items = paths.map((path) => {
    const url = new URL(path, origin);
    if (url.origin !== origin || url.href.length >= 2048) throw new SitemapError();
    const escaped = url.href.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    return `<${entry}><loc>${escaped}</loc></${entry}>`;
  });
  const xml = `<?xml version="1.0" encoding="UTF-8"?><${name} xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">${items.join('')}</${name}>`;
  if (new TextEncoder().encode(xml).byteLength > 52428800) throw new SitemapError();
  return xml;
}

export function editorialXml(categories: SitemapCategories, origin: string) {
  if (categories.length > 4998) throw new SitemapError();
  return sitemapXml(['/produtos', '/categorias', ...categories.map(({ slug }) => `/produtos?category=${encodeURIComponent(slug)}`)], origin);
}
