import type { Metadata } from 'next';
import type { ReactNode } from 'react';

import './globals.css';

export const metadata: Metadata = {
  title: {
    default: 'JS Designs',
    template: '%s | JS Designs',
  },
  description:
    'Loja online JS Designs para lembrancinhas físicas personalizadas, convites digitais e produtos digitais.',
};

export default function RootLayout({ children }: Readonly<{ children: ReactNode }>) {
  return (
    <html lang="pt-BR">
      <body>{children}</body>
    </html>
  );
}
