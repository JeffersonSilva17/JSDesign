import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.entrega.metadata;

export default function EntregaPage() {
  return <PlaceholderPage pageKey="entrega" />;
}
