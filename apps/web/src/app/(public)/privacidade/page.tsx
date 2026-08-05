import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.privacidade.metadata;

export default function PrivacidadePage() {
  return <PlaceholderPage pageKey="privacidade" />;
}
