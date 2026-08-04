import Link from 'next/link';

import { mainNavItems, publicCta } from '@/features/public-store/publicLayoutContent';

export function SiteHeader() {
  return (
    <header className="site-header">
      <div className="site-header__inner">
        <Link className="brand-link" href="/" aria-label="JS Designs, voltar para a home">
          <span className="brand-link__mark" aria-hidden="true">
            JS
          </span>
          <span className="brand-link__text">
            <strong>JS Designs</strong>
            <span>celebrações autorais</span>
          </span>
        </Link>

        <nav className="primary-nav" aria-label="Navegação principal">
          <span className="primary-nav__label" aria-hidden="true">
            Menu
          </span>
          <ul className="primary-nav__list">
            {mainNavItems.map((item) => (
              <li key={item.href}>
                <Link href={item.href}>{item.label}</Link>
              </li>
            ))}
          </ul>
        </nav>

        <Link className="button button--primary site-header__cta" href={publicCta.href}>
          {publicCta.label}
        </Link>
      </div>
    </header>
  );
}
