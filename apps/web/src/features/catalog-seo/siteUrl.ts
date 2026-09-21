import 'server-only';

import { parseSeoConfig } from './seoConfig';

export function seoConfig() {
  return parseSeoConfig(process.env.SITE_URL, process.env.SEO_INDEXING_ENABLED);
}

export function publicUrl(path: string): string {
  return new URL(path, seoConfig().origin).href;
}
