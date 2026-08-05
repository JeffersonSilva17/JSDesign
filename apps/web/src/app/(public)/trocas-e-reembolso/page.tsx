import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';
import { placeholderPages } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = placeholderPages['trocas-e-reembolso'].metadata;

export default function TrocasEReembolsoPage() {
  return <PlaceholderPage pageKey="trocas-e-reembolso" />;
}
