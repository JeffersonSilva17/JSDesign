import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.politicas.metadata;

export default function PoliticasPage() {
  return <PlaceholderPage pageKey="politicas" />;
}
