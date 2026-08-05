import type { Metadata } from 'next';
import type { ReactNode } from 'react';

import { activeLocale, publicContent } from '@/features/public-store/publicLayoutContent';

import './globals.css';

export const metadata: Metadata = {
  title: {
    default: publicContent.metadata.root.title,
    template: `%s | ${publicContent.brand.name}`,
  },
  description: publicContent.metadata.root.description,
};

export default function RootLayout({ children }: Readonly<{ children: ReactNode }>) {
  return (
    <html lang={activeLocale}>
      <body>{children}</body>
    </html>
  );
}
