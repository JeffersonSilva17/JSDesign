import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Busca em preparação',
  description:
    'Busca pública da JS Designs em preparação para temas, produtos e ocasiões.',
};

export default function BuscarPage() {
  return <PlaceholderPage pageKey="buscar" />;
}
