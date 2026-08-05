import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.suporte.metadata;

export default function SuportePage() {
  return <PlaceholderPage pageKey="suporte" />;
}
