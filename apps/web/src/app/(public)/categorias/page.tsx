import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Categorias em preparação',
  description:
    'Categorias públicas da JS Designs em preparação para lembrancinhas, convites e produtos digitais.',
};

export default function CategoriasPage() {
  return <PlaceholderPage pageKey="categorias" />;
}
