import type { Metadata } from 'next';
import Link from 'next/link';

import { publicContent, publicCta, secondaryPublicCta } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = publicContent.metadata.home;

export default function Home() {
  const { hero, modalityCards, nextPaths } = publicContent.home;

  return (
    <>
      <section className="hero" aria-labelledby="home-title">
        <div className="hero__copy">
          <p className="eyebrow">{hero.eyebrow}</p>
          <h1 id="home-title">{hero.title}</h1>
          <p className="hero__lead">{hero.lead}</p>
          <div className="hero__actions">
            <Link className="button button--primary" href={publicCta.href}>
              {publicCta.label}
            </Link>
            <Link className="button button--secondary" href={secondaryPublicCta.href}>
              {secondaryPublicCta.label}
            </Link>
          </div>
        </div>

        <aside className="hero__visual" aria-label={hero.visualAriaLabel}>
          {modalityCards.map((card, index) => (
            <div
              className={[
                'product-preview',
                index === 0 ? 'product-preview--large' : '',
                'tone' in card && card.tone === 'accent' ? 'product-preview--accent' : '',
              ]
                .filter(Boolean)
                .join(' ')}
              key={card.label}
            >
              <span>{card.label}</span>
              <strong>{card.title}</strong>
              <p>{card.description}</p>
            </div>
          ))}
        </aside>
      </section>

      <section className="entry-section" aria-labelledby="entry-title">
        <div>
          <p className="eyebrow">{nextPaths.eyebrow}</p>
          <h2 id="entry-title">{nextPaths.title}</h2>
        </div>
        <p>{nextPaths.description}</p>
      </section>
    </>
  );
}
