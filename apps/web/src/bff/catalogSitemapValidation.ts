import { SitemapError } from './catalogSitemapTransport.ts';

export type SitemapPage = Readonly<{ data: readonly Readonly<{ slug: string }>[]; meta: Readonly<{ current_page: number; per_page: number; last_page: number; total: number }> }>;
export type SitemapCategories = readonly Readonly<{ slug: string; label: string }>[];
const record = (value: unknown): value is Record<string, unknown> => !!value && typeof value === 'object' && !Array.isArray(value);
const keys = (value: Record<string, unknown>, expected: string[]) => Object.keys(value).length === expected.length && expected.every((key) => key in value);
const slug = (value: unknown, max: number): value is string => typeof value === 'string' && value.length <= max && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value);

export function sitemapPage(value: unknown, page: number): SitemapPage {
  if (!record(value) || !keys(value, ['data', 'meta']) || !Array.isArray(value.data) || !record(value.meta)) throw new SitemapError();
  const meta = value.meta;
  if (!keys(meta, ['current_page', 'per_page', 'last_page', 'total']) || meta.current_page !== page || meta.per_page !== 500 ||
      !Number.isSafeInteger(meta.total) || (meta.total as number) < 0 || (meta.total as number) > 5000000 ||
      meta.last_page !== Math.max(1, Math.ceil((meta.total as number) / 500)) || page > (meta.last_page as number) ||
      value.data.length !== Math.min(500, Math.max(0, (meta.total as number) - (page - 1) * 500))) throw new SitemapError();
  const seen = new Set<string>();
  for (const item of value.data) {
    if (!record(item) || !keys(item, ['slug']) || !slug(item.slug, 180) || seen.has(item.slug)) throw new SitemapError();
    seen.add(item.slug);
  }
  return value as SitemapPage;
}

export function sitemapCategories(value: unknown): SitemapCategories {
  if (!record(value) || !keys(value, ['data']) || !record(value.data) || !keys(value.data, ['categories']) || !Array.isArray(value.data.categories) || value.data.categories.length > 4998) throw new SitemapError();
  const seen = new Set<string>();
  for (const item of value.data.categories) {
    if (!record(item) || !keys(item, ['slug', 'label']) || !slug(item.slug, 160) || typeof item.label !== 'string' || !item.label.trim() || seen.has(item.slug)) throw new SitemapError();
    seen.add(item.slug);
  }
  return value.data.categories as SitemapCategories;
}
