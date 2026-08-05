import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.produtos.metadata;

export default function ProdutosPage() {
  return <PlaceholderPage pageKey="produtos" />;
}
