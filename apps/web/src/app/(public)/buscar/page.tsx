import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.buscar.metadata;

export default function BuscarPage() {
  return <PlaceholderPage pageKey="buscar" />;
}
