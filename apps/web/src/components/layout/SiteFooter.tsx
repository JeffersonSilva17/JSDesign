import Link from 'next/link';

import { footerSections } from '@/features/public-store/publicLayoutContent';

export function SiteFooter() {
  return (
    <footer className="site-footer">
      <div className="site-footer__inner">
        <section className="site-footer__brand" aria-labelledby="footer-brand-title">
          <p className="eyebrow">JS Designs</p>
          <h2 id="footer-brand-title">Papelaria afetiva com direção autoral.</h2>
          <p>
            Loja online em construção para lembrancinhas físicas personalizadas, convites
            digitais e arquivos digitais claramente identificados.
          </p>
          <p>
            Para contato e suporte, use a página de Suporte. O atendimento completo será conectado
            dentro do site em etapa própria.
          </p>
          <p className="trust-copy">Pagamentos protegidos e processados por parceiros certificados.</p>
        </section>

        <nav className="site-footer__nav" aria-label="Links do rodapé">
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
        <p>© 2026 JS Designs. Loja online em implementação.</p>
      </div>
    </footer>
  );
}
