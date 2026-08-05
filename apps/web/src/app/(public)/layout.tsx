import type { ReactNode } from 'react';

import { PublicShell } from '@/components/layout/PublicShell';

export default function PublicLayout({ children }: Readonly<{ children: ReactNode }>) {
  return <PublicShell>{children}</PublicShell>;
}
