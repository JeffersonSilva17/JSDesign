import Link from 'next/link';

import { footerSections, publicContent } from '@/features/public-store/publicLayoutContent';

export function SiteFooter() {
  const { footer } = publicContent;

  return (
    <footer className="site-footer">
      <div className="site-footer__inner">
        <section className="site-footer__brand" aria-labelledby="footer-brand-title">
          <p className="eyebrow">{footer.eyebrow}</p>
          <h2 id="footer-brand-title">{footer.title}</h2>
          <p>{footer.description}</p>
          <p>{footer.support}</p>
          <p className="trust-copy">{footer.trustCopy}</p>
        </section>

        <nav className="site-footer__nav" aria-label={footer.navAriaLabel}>
          {footerSections.map((section) => (
            <section key={section.id} aria-labelledby={`footer-${section.id}`}>
              <h3 id={`footer-${section.id}`}>{section.title}</h3>
              <ul>
                {section.links.map((link) => (
                  <li key={link.href}>
                    <Link href={link.href}>{link.label}</Link>
                  </li>
                ))}
              </ul>
            </section>
          ))}
        </nav>
      </div>

      <div className="site-footer__bottom">
        <p>{footer.copyright}</p>
      </div>
    </footer>
  );
}
