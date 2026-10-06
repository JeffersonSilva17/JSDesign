import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { notFound } from 'next/navigation';

import { CatalogApiError } from '@/bff/catalogApi';
import type { CatalogImage, CatalogProductModel } from '@/bff/catalogApi';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

import { readProduct } from '@/features/catalog-seo/catalogReads';
import { catalogMetadata } from '@/features/catalog-seo/catalogMetadata';
import { detailQuery } from '@/features/catalog-seo/detailQuery';
import { productStructuredData } from '@/features/catalog-seo/catalogStructuredData';
import { StructuredData } from '@/features/catalog-seo/StructuredData';
import { seoConfig } from '@/features/catalog-seo/siteUrl';
import { PricingConfigurator } from '@/features/pricing/PricingConfigurator';
import { defaultLocale } from '@/i18n/locales';

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
  const query = detailQuery(await searchParams);
  if (!product || !query.valid) return catalogMetadata(catalogContent.metadata.detail.title, catalogContent.metadata.detail.description);
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
  const query = detailQuery(await searchParams);
  const returnHref = query.returnHref;
  const modelKey = 'modelKey' in query ? query.modelKey ?? null : null;
  const selectedModel = selectedProductModel(product.models, modelKey);
  const gallery = product.gallery.length > 0 ? product.gallery : product.primary_image ? [product.primary_image] : [];
  const ctaHref = `/carrinho?produto=${encodeURIComponent(product.slug)}&modelo=${encodeURIComponent(selectedModel.key)}`;
  return <article className="catalog-detail">
    {seoConfig().indexing && query.valid && <StructuredData value={productStructuredData(product, seoConfig().origin)} />}
    <section className="catalog-detail__gallery" aria-label={catalogContent.detail.gallery}>
      <div className="catalog-detail__media">
        {gallery[0] ? <Image src={gallery[0].url} alt={gallery[0].alt_text} fill sizes="(min-width: 1100px) 48vw, (min-width: 760px) 50vw, 100vw" preload /> : <span>{catalogContent.card.unavailableImage}</span>}
      </div>
      {gallery.length > 1 && <ul className="catalog-detail__thumbs">
        {gallery.map((image, index) => <li key={`${image.url}-${index}`}><ProductImage image={image} /></li>)}
      </ul>}
    </section>
    <div className="catalog-detail__body">
      <p className="eyebrow">{catalogContent.detail.eyebrow}</p>
      <h1>{product.name}</h1>
      <div className="catalog-detail__badges">
        <span>{catalogContent.modality[product.modality]}</span>
        <span>{catalogContent.availability[product.availability]}</span>
        {product.is_immediate_delivery && <span>{catalogContent.card.immediate}</span>}
      </div>
      <p className="catalog-detail__description">{product.description}</p>
      <p className="catalog-detail__modality-note">{catalogContent.detail.modalityNotes[product.modality]}</p>
      <dl className="catalog-detail__facts" aria-label={catalogContent.detail.summary}>
        <Fact label={catalogContent.filters.category} value={product.category.label} />
        {product.production_lead_time_days !== null && <Fact label={catalogContent.detail.leadTime} value={`${product.production_lead_time_days} ${catalogContent.detail.days}`} />}
        {product.minimum_quantity !== null && <Fact label={catalogContent.detail.minimumQuantity} value={`${product.minimum_quantity} ${catalogContent.detail.units}`} />}
        {product.materials && <Fact label={catalogContent.detail.materials} value={product.materials} />}
        {product.composition && <Fact label={catalogContent.detail.composition} value={product.composition} />}
        {product.file_description && <Fact label={catalogContent.detail.fileDescription} value={product.file_description} />}
        {product.compatibility && <Fact label={catalogContent.detail.compatibility} value={product.compatibility} />}
      </dl>
      <PricingConfigurator key={`${product.slug}:${product.models.length > 0 ? selectedModel.key : ''}:${product.minimum_quantity ?? ''}:${product.modality}:${defaultLocale}`} slug={product.slug} modelKey={product.models.length > 0 ? selectedModel.key : null}
        minimumQuantity={product.minimum_quantity} modality={product.modality} ctaHref={ctaHref} locale={defaultLocale} />
      {product.usage_terms && <p className="catalog-detail__terms"><strong>{catalogContent.detail.usageTerms}:</strong> {product.usage_terms} <Link href="/termos">{catalogContent.detail.termsLink}</Link></p>}
      <section className="catalog-detail__models" aria-labelledby="product-models-title">
        <h2 id="product-models-title">{catalogContent.detail.models}</h2>
        <ul>
          {product.models.map((model) => {
            const selected = model.key === selectedModel.key;
            return <li key={model.key}>
              <Link href={modelHref(product.slug, model.key, returnHref)} aria-current={selected ? 'true' : undefined} className={selected ? 'is-selected' : undefined}>
                <span>{model.label}</span>
                <small>{model.difference}</small>
                <strong>{product.models.length === 1 ? catalogContent.detail.uniqueModel : selected ? catalogContent.detail.selected : ''}</strong>
              </Link>
            </li>;
          })}
        </ul>
      </section>
      <div className="catalog-detail__actions">
        <Link className="button button--secondary" href={returnHref}>{catalogContent.detail.back}</Link>
      </div>
    </div>
  </article>;
}

function ProductImage({ image }: Readonly<{ image: CatalogImage }>) {
  return <span className="catalog-detail__thumb"><Image src={image.url} alt={image.alt_text} fill sizes="96px" /></span>;
}

function Fact({ label, value, strong = false }: Readonly<{ label: string; value: string; strong?: boolean }>) {
  return <div><dt>{label}</dt><dd>{strong ? <strong>{value}</strong> : value}</dd></div>;
}

function selectedProductModel(models: readonly CatalogProductModel[], key: string | null): CatalogProductModel {
  const selected = models.find((model) => model.key === key) ?? models.find((model) => model.is_default) ?? models[0];
  if (!selected) throw new Error('Product detail payload missing public model.');
  return selected;
}

function modelHref(slug: string, modelKey: string, returnHref: string): string {
  const params = new URLSearchParams({ modelo: modelKey });
  if (returnHref !== '/produtos') params.set('return_to', returnHref);
  return `/produtos/${slug}?${params.toString()}`;
}
