import { CatalogApiError } from './catalogErrors.ts';
import type { CatalogFilters, CatalogModality } from './catalogApi';

type SearchParams = Readonly<Record<string, string | string[] | undefined>>;
const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
const maxPage = 10000;
const modalities: readonly CatalogModality[] = [
  'physical_personalized',
  'digital_personalized',
  'digital_ready',
];

export function parseCatalogSearchParams(input: SearchParams): CatalogFilters {
  const allowed = new Set(['category', 'occasion', 'modality', 'page']);
  if (Object.keys(input).some((key) => !allowed.has(key))) throw new CatalogApiError('invalid-filter');
  for (const value of Object.values(input)) {
    if (Array.isArray(value) || (typeof value === 'string' && value.trim() === '')) {
      throw new CatalogApiError('invalid-filter');
    }
  }
  const category = optionalSlug(input.category, 160);
  const occasion = optionalOccasion(input.occasion);
  const modality = input.modality;
  if (modality !== undefined && !modalities.includes(modality as CatalogModality)) {
    throw new CatalogApiError('invalid-filter');
  }
  const rawPage = input.page ?? '1';
  if (typeof rawPage !== 'string') throw new CatalogApiError('invalid-filter');
  if (!/^[1-9][0-9]*$/.test(rawPage) || !Number.isSafeInteger(Number(rawPage)) || Number(rawPage) > maxPage) {
    throw new CatalogApiError('invalid-filter');
  }
  return { category, occasion, modality: modality as CatalogModality | undefined, page: Number(rawPage) };
}

export function catalogHref(filters: CatalogFilters, changes: Partial<CatalogFilters> = {}): string {
  const merged = { ...filters, ...changes };
  const url = new URL('/produtos', 'http://same-origin.invalid');
  if (merged.category) url.searchParams.set('category', merged.category);
  if (merged.occasion) url.searchParams.set('occasion', merged.occasion);
  if (merged.modality) url.searchParams.set('modality', merged.modality);
  if (merged.page > 1) url.searchParams.set('page', String(merged.page));
  return `${url.pathname}${url.search}`;
}

function optionalSlug(value: string | string[] | undefined, max: number): string | undefined {
  if (value === undefined) return undefined;
  if (typeof value !== 'string' || value.length > max || !slugPattern.test(value)) {
    throw new CatalogApiError('invalid-filter');
  }
  return value;
}

function optionalOccasion(value: string | string[] | undefined): string | undefined {
  if (value === undefined) return undefined;
  if (typeof value !== 'string' || value.length > 180 || value.trim() === '') throw new CatalogApiError('invalid-filter');
  const canonical = value.normalize('NFC').trim().replace(/\s+/gu, ' ').toLocaleLowerCase('pt-BR')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '').normalize('NFC');
  if (canonical !== value) throw new CatalogApiError('invalid-filter');
  return value;
}
