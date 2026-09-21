import type { NextConfig } from 'next';

import { parseSeoConfig } from './src/features/catalog-seo/seoConfig';

const seo = parseSeoConfig(process.env.SITE_URL, process.env.SEO_INDEXING_ENABLED);

const nextConfig: NextConfig = {
  distDir: process.env.NEXT_DIST_DIR ?? '.next',
  reactStrictMode: true,
  async headers() {
    return seo.indexing ? [] : [{ source: '/:path*', headers: [{ key: 'X-Robots-Tag', value: 'noindex, nofollow' }] }];
  },
};

export default nextConfig;
