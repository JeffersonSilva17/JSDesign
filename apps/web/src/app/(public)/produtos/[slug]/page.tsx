import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { notFound } from 'next/navigation';

import { CatalogApiError, fetchCatalogProduct, parseCatalogSearchParams, parsePublicSearchParams } from '@/bff/catalogApi';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

type Props = Readonly<{
  params: Promise<{ slug: string }>;
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}>;

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  try {
    const product = await fetchCatalogProduct((await params).slug);
    return {
      ...catalogContent.metadata.detail,
      title: product.name,
      description: product.description.slice(0, 155),
    };
  } catch {
    return catalogContent.metadata.detail;
  }
}

export default async function ProductDetailPage({ params, searchParams }: Props) {
  let product;
  try {
    product = await fetchCatalogProduct((await params).slug);
  } catch (error) {
    if (error instanceof CatalogApiError && error.kind === 'not-found') notFound();
    product = null;
  }
  if (product === null) return <CatalogState kind="unavailable" />;
  const returnHref = safeReturnHref((await searchParams).return_to);
  const price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: product.currency }).format(product.price_minor / 100);
  return <article className="catalog-detail">
    <div className="catalog-detail__media">{product.primary_image ? <Image src={product.primary_image.url} alt={product.primary_image.alt_text} fill sizes="(min-width: 760px) 45vw, 100vw" /> : <span>{catalogContent.card.unavailableImage}</span>}</div>
    <div><p className="eyebrow">{catalogContent.detail.eyebrow}</p><h1>{product.name}</h1><p className="catalog-card__modality">{catalogContent.modality[product.modality]}</p><p className="catalog-card__availability">{catalogContent.availability[product.availability]}</p><p>{product.description}</p>{product.compatibility && <p>{product.compatibility}</p>}<p className="catalog-card__price">{price}</p><Link className="button button--secondary" href={returnHref}>{catalogContent.detail.back}</Link></div>
  </article>;
}

function safeReturnHref(value: string | string[] | undefined): string {
  if (typeof value !== 'string') return '/produtos';
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
