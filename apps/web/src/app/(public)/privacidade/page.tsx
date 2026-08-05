import type { Metadata } from 'next';

import { PlaceholderPage } from '@/components/layout/PlaceholderPage';

export const metadata: Metadata = {
  title: 'Privacidade em preparação',
  description:
    'Política de privacidade da JS Designs em preparação, sem coleta de dados pessoais nesta página.',
};

export default function PrivacidadePage() {
  return <PlaceholderPage pageKey="privacidade" />;
}
