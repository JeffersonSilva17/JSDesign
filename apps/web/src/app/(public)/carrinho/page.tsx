import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Carrinho em preparação',
  description:
    'Carrinho da JS Designs em preparação, sem simular itens, subtotal, cupom ou checkout.',
  robots: {
    follow: false,
    index: false,
  },
};

export default function CarrinhoPage() {
  return <PlaceholderPage pageKey="carrinho" />;
}
