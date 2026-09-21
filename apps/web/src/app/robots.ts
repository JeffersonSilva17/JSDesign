import type { MetadataRoute } from 'next';

import { publicUrl, seoConfig } from '@/features/catalog-seo/siteUrl';

export const dynamic = 'force-dynamic';

export default function robots(): MetadataRoute.Robots {
  return { rules: { userAgent: '*', allow: '/' }, ...(seoConfig().indexing ? { sitemap: publicUrl('/sitemap.xml') } : {}) };
}
