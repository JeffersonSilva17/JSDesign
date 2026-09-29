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
  const keys = Object.keys(input);
  const allowed = keys.every((key) => key === 'return_to' || key === 'modelo');
  const validReturn = value === undefined || (typeof value === 'string' && (href !== '/produtos' || value === '/produtos'));
  const modelKey = typeof input.modelo === 'string' && input.modelo.length <= 120 && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(input.modelo)
    ? input.modelo
    : null;
  const valid = allowed && validReturn;
  return input.modelo === undefined
    ? { valid, returnHref: valid ? href : '/produtos' }
    : { valid, returnHref: valid ? href : '/produtos', modelKey };
}
