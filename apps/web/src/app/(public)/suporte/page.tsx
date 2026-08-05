import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Suporte em preparação',
  description:
    'Suporte da JS Designs em preparação para atendimento dentro do site.',
};

export default function SuportePage() {
  return <PlaceholderPage pageKey="suporte" />;
}
