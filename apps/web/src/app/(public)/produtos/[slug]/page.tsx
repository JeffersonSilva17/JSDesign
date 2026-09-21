import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { notFound } from 'next/navigation';

import { CatalogApiError } from '@/bff/catalogApi';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

import { readProduct } from '@/features/catalog-seo/catalogReads';
import { catalogMetadata } from '@/features/catalog-seo/catalogMetadata';
import { detailQuery } from '@/features/catalog-seo/detailQuery';
import { productStructuredData } from '@/features/catalog-seo/catalogStructuredData';
import { StructuredData } from '@/features/catalog-seo/StructuredData';
import { seoConfig } from '@/features/catalog-seo/siteUrl';

type Props = Readonly<{
  params: Promise<{ slug: string }>;
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}>;

export async function generateMetadata({ params, searchParams }: Props): Promise<Metadata> {
  let product;
  let missing = false;
  try { product = await readProduct((await params).slug); }
  catch (error) { missing = error instanceof CatalogApiError && error.kind === 'not-found'; }
  if (missing) notFound();
  if (!product || !detailQuery(await searchParams).valid) return catalogMetadata(catalogContent.metadata.detail.title, catalogContent.metadata.detail.description);
  return catalogMetadata(product.name, product.description.slice(0, 155), `/produtos/${product.slug}`, true, product.primary_image);
}

export default async function ProductDetailPage({ params, searchParams }: Props) {
  let product;
  try {
    product = await readProduct((await params).slug);
  } catch (error) {
    if (error instanceof CatalogApiError && error.kind === 'not-found') notFound();
    product = null;
  }
  if (product === null) return <CatalogState kind="unavailable" />;
  const returnHref = detailQuery(await searchParams).returnHref;
  const price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: product.currency }).format(product.price_minor / 100);
  return <article className="catalog-detail">
    {seoConfig().indexing && detailQuery(await searchParams).valid && <StructuredData value={productStructuredData(product, seoConfig().origin)} />}
    <div className="catalog-detail__media">{product.primary_image ? <Image src={product.primary_image.url} alt={product.primary_image.alt_text} fill sizes="(min-width: 760px) 45vw, 100vw" /> : <span>{catalogContent.card.unavailableImage}</span>}</div>
    <div><p className="eyebrow">{catalogContent.detail.eyebrow}</p><h1>{product.name}</h1><p className="catalog-card__modality">{catalogContent.modality[product.modality]}</p><p className="catalog-card__availability">{catalogContent.availability[product.availability]}</p><p>{product.description}</p>{product.compatibility && <p>{product.compatibility}</p>}<p className="catalog-card__price">{price}</p><Link className="button button--secondary" href={returnHref}>{catalogContent.detail.back}</Link></div>
  </article>;
}
