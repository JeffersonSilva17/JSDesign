import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Termos em preparação',
  description:
    'Termos da loja JS Designs em preparação para publicação antes do lançamento.',
};

export default function TermosPage() {
  return <PlaceholderPage pageKey="termos" />;
}
