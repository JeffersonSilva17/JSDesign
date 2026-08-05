import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.termos.metadata;

export default function TermosPage() {
  return <PlaceholderPage pageKey="termos" />;
}
