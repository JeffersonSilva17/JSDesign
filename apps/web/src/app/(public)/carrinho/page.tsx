import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.carrinho.metadata;

export default function CarrinhoPage() {
  return <PlaceholderPage pageKey="carrinho" />;
}
