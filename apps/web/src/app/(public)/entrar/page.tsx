import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages.entrar.metadata;

export default function EntrarPage() {
  return <PlaceholderPage pageKey="entrar" />;
}
