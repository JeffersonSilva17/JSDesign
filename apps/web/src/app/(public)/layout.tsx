import type { ReactNode } from 'react';

import { PublicShell } from '@/components/layout/PublicShell';
import { FirstPurchaseDiscountDialog } from '@/features/first-purchase-discount/FirstPurchaseDiscountDialog';

export default function PublicLayout({ children }: Readonly<{ children: ReactNode }>) {
  return <PublicShell globalSurface={<FirstPurchaseDiscountDialog />}>{children}</PublicShell>;
}
