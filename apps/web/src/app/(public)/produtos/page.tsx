import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Produtos em preparação',
  description:
    'Entrada pública do catálogo JS Designs em preparação, sem simular preço ou disponibilidade.',
};

export default function ProdutosPage() {
  return <PlaceholderPage pageKey="produtos" />;
}
