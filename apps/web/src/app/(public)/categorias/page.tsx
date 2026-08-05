import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.categorias.metadata;

export default function CategoriasPage() {
  return <PlaceholderPage pageKey="categorias" />;
}
