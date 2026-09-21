import type { Metadata } from 'next';
import type { ReactNode } from 'react';

import { activeLocale, publicContent } from '@/features/public-store/publicLayoutContent';
import { seoConfig } from '@/features/catalog-seo/siteUrl';

import './globals.css';

export const metadata: Metadata = {
  metadataBase: new URL(seoConfig().origin),
  robots: seoConfig().indexing ? undefined : { index: false, follow: false },
  title: {
    default: publicContent.metadata.root.title,
    template: `%s | ${publicContent.brand.name}`,
  },
  description: publicContent.metadata.root.description,
  referrer: 'strict-origin-when-cross-origin',
};

export default function RootLayout({ children }: Readonly<{ children: ReactNode }>) {
  return (
    <html lang={activeLocale}>
      <body>{children}</body>
    </html>
  );
}
