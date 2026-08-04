import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Entrega em preparação',
  description:
    'Informações de entrega da JS Designs em preparação, sem cálculo de prazo ou frete nesta etapa.',
};

export default function EntregaPage() {
  return <PlaceholderPage pageKey="entrega" />;
}
