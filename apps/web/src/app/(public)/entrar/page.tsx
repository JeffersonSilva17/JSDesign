import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Área da Cliente em preparação',
  description:
    'Área da Cliente da JS Designs em preparação, sem autenticação ou dados privados nesta etapa.',
  robots: {
    follow: false,
    index: false,
  },
};

export default function EntrarPage() {
  return <PlaceholderPage pageKey="entrar" />;
}
