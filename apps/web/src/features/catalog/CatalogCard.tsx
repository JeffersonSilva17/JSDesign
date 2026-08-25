import Image from 'next/image';
import Link from 'next/link';

import type { CatalogCard as CatalogCardData } from '@/bff/catalogApi';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

export function CatalogCard({ product, preload = false, returnHref = '/produtos' }: Readonly<{ product: CatalogCardData; preload?: boolean; returnHref?: string }>) {
  const price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: product.currency }).format(product.price_minor / 100);
  const detailHref = `/produtos/${encodeURIComponent(product.slug)}?return_to=${encodeURIComponent(returnHref)}`;
  return (
    <article className="catalog-card" aria-labelledby={`product-${product.id}`}>
      <div className="catalog-card__media">
        {product.primary_image ? <Image src={product.primary_image.url} alt={product.primary_image.alt_text} fill preload={preload} sizes="(min-width: 1100px) 25vw, (min-width: 760px) 33vw, 100vw" /> : <span>{catalogContent.card.unavailableImage}</span>}
      </div>
      <div className="catalog-card__body">
        <p className="catalog-card__modality">{catalogContent.modality[product.modality]}</p>
        <p className="catalog-card__availability">{catalogContent.availability[product.availability]}</p>
        <h2 id={`product-${product.id}`}>{product.name}</h2>
        <p>{product.description_excerpt}</p>
        {product.compatibility_excerpt && <p>{product.compatibility_excerpt}</p>}
        {product.is_immediate_delivery && <p className="catalog-card__delivery">{catalogContent.card.immediate}</p>}
        {product.production_lead_time_days !== null && <p>{catalogContent.card.leadTime}: {product.production_lead_time_days} dias</p>}
        <p className="catalog-card__price">{price}</p>
        <Link className="button button--primary" href={detailHref} aria-label={`${catalogContent.card.details}: ${product.name}`}>{catalogContent.card.details}</Link>
      </div>
    </article>
  );
}
