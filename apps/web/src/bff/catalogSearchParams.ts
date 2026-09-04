import { CatalogApiError } from './catalogErrors.ts';

export type CatalogSearchCriteria = Readonly<{ query: string | null; page: number }>;
type SearchParams = Readonly<Record<string, string | string[] | undefined>>;

export function parsePublicSearchParams(input: SearchParams): CatalogSearchCriteria {
  if (Object.keys(input).some((key) => key !== 'q' && key !== 'page')) throw new CatalogApiError('invalid-filter');
  if (Array.isArray(input.q) || Array.isArray(input.page)) throw new CatalogApiError('invalid-filter');
  const rawPage = input.page ?? '1';
  if (!/^[1-9][0-9]*$/.test(rawPage) || !Number.isSafeInteger(Number(rawPage)) || Number(rawPage) > 1000) {
    throw new CatalogApiError('invalid-filter');
  }
  if (input.q === undefined) {
    if (input.page !== undefined) throw new CatalogApiError('invalid-filter');
    return { query: null, page: 1 };
  }
  if (/[\p{Cc}\p{Cf}]/u.test(input.q)) throw new CatalogApiError('invalid-filter');
  const query = input.q.normalize('NFKC').trim().replace(/[\p{Z}\s]+/gu, ' ');
  const length = Array.from(query).length;
  if (length < 2 || length > 120 || new TextEncoder().encode(query).length > 512) {
    throw new CatalogApiError('invalid-filter');
  }
  return { query, page: Number(rawPage) };
}

export function publicSearchHref(criteria: CatalogSearchCriteria, page = criteria.page): string {
  const url = new URL('/buscar', 'http://same-origin.invalid');
  if (criteria.query !== null) url.searchParams.set('q', criteria.query);
  if (criteria.query !== null && page > 1) url.searchParams.set('page', String(page));
  return `${url.pathname}${url.search}`;
}
