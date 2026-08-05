import type { ReactNode } from 'react';

import { SiteFooter } from '@/components/layout/SiteFooter';
import { SiteHeader } from '@/components/layout/SiteHeader';

type PublicShellProps = Readonly<{
  children: ReactNode;
  globalSurface?: ReactNode;
}>;

export function PublicShell({ children, globalSurface }: PublicShellProps) {
  return (
    <div className="public-shell">
      <a className="skip-link" href="#conteudo">
        Pular para o conteúdo principal
      </a>
      <SiteHeader />
      <div className="global-surface">{globalSurface}</div>
      <main id="conteudo" className="site-main" tabIndex={-1}>
        {children}
      </main>
      <SiteFooter />
    </div>
  );
}
