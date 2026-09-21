import { parseCatalogSearchParams } from '../../bff/catalogParams.ts';
import { parsePublicSearchParams } from '../../bff/catalogSearchParams.ts';

export function safeReturnHref(value: string | string[] | undefined): string {
  if (typeof value !== 'string' || !/^\/(produtos|buscar)(?:\?|$)/.test(value) || /[\\#\u0000-\u0020]/u.test(value) || /%(?![a-f0-9]{2})/i.test(value)) return '/produtos';
  try {
    const url = new URL(value, 'http://same-origin.invalid');
    if (url.origin !== 'http://same-origin.invalid' || !['/produtos', '/buscar'].includes(url.pathname)) return '/produtos';
    const params: Record<string, string> = {};
    const seen = new Set<string>();
    for (const [key, paramValue] of url.searchParams.entries()) {
      if (seen.has(key)) return '/produtos';
      seen.add(key);
      params[key] = paramValue;
    }
    if (url.pathname === '/buscar') {
      const criteria = parsePublicSearchParams(params);
      if (criteria.query === null) return '/produtos';
    } else {
      parseCatalogSearchParams(params);
    }
    return `${url.pathname}${url.search}`;
  } catch {
    return '/produtos';
  }
}

export function detailQuery(input: Readonly<Record<string, string | string[] | undefined>>) {
  if (Object.keys(input).length === 0) return { valid: true, returnHref: '/produtos' };
  const value = input.return_to;
  const href = safeReturnHref(value);
  const valid = Object.keys(input).length === 1 && typeof value === 'string' && (href !== '/produtos' || value === '/produtos');
  return { valid, returnHref: valid ? href : '/produtos' };
}
