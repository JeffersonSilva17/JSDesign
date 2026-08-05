import type { Metadata } from 'next';
import Link from 'next/link';

import { publicCta } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = {
  title: 'Loja online de personalizados físicos e digitais',
  description:
    'Loja online JS Designs para lembrancinhas físicas personalizadas, convites digitais e produtos digitais com experiência mobile-first.',
};

export default function Home() {
  return (
    <>
      <section className="hero" aria-labelledby="home-title">
        <div className="hero__copy">
          <p className="eyebrow">Loja JS Designs</p>
          <h1 id="home-title">Detalhes personalizados para celebrar com cuidado.</h1>
          <p className="hero__lead">
            Uma experiência clara para descobrir lembrancinhas físicas personalizadas, convites
            digitais e arquivos digitais sem depender de atendimento para começar.
          </p>
          <div className="hero__actions">
            <Link className="button button--primary" href={publicCta.href}>
              {publicCta.label}
            </Link>
            <Link className="button button--secondary" href="/buscar">
              Buscar por tema
            </Link>
          </div>
        </div>

        <aside className="hero__visual" aria-label="Resumo visual da loja JS Designs">
          <div className="product-preview product-preview--large">
            <span>Lembrancinhas físicas</span>
            <strong>Personalização antes do carrinho</strong>
          </div>
          <div className="product-preview">
            <span>Convites digitais</span>
            <strong>Prévia e aprovação</strong>
          </div>
          <div className="product-preview product-preview--accent">
            <span>Produto digital</span>
            <strong>Identificado com clareza</strong>
          </div>
        </aside>
      </section>

      <section className="entry-section" aria-labelledby="entry-title">
        <div>
          <p className="eyebrow">Próximos caminhos</p>
          <h2 id="entry-title">Base pronta para descoberta de produtos.</h2>
        </div>
        <p>
          A listagem de “Mais procurados” entra na Story 1.4. Nesta etapa, a loja já apresenta
          identidade, navegação e estrutura pública para receber catálogo, busca e compra.
        </p>
      </section>
    </>
  );
}
