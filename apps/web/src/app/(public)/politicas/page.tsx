import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Políticas em preparação',
  description:
    'Políticas da loja JS Designs em preparação para publicação antes do lançamento.',
};

export default function PoliticasPage() {
  return <PlaceholderPage pageKey="politicas" />;
}
