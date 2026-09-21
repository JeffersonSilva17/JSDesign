import { sitemapResponse } from '@/features/catalog-seo/sitemapHandler';

export const dynamic = 'force-dynamic';
export function GET(request: Request) { return sitemapResponse(request, 'editorial'); }
