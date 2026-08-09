import type { Metadata } from 'next';
import Link from 'next/link';

import { publicContent, publicCta, secondaryPublicCta } from '@/features/public-store/publicLayoutContent';

export const metadata: Metadata = publicContent.metadata.home;

export default function Home() {
  const { hero, mostWanted, nextPaths } = publicContent.home;

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
      </section>

      <section className="most-wanted" aria-labelledby="most-wanted-title">
        <div className="most-wanted__header">
          <p className="eyebrow">{mostWanted.eyebrow}</p>
          <h2 id="most-wanted-title">{mostWanted.title}</h2>
          <p>{mostWanted.description}</p>
        </div>

        <div className="most-wanted__grid">
          {mostWanted.items.map((item, index) => (
            <article
              className="most-wanted-card"
              aria-labelledby={`most-wanted-item-${index}`}
              key={item.title}
            >
              <div
                className={`most-wanted-card__visual most-wanted-card__visual--${item.tone}`}
                aria-hidden="true"
              >
                <span>{item.visualLabel}</span>
              </div>
              <div className="most-wanted-card__body">
                <p className="most-wanted-card__modality">{item.modality}</p>
                <h3 id={`most-wanted-item-${index}`}>{item.title}</h3>
                <p>{item.description}</p>
                <Link
                  className="button button--secondary most-wanted-card__cta"
                  href={item.cta.href}
                  aria-label={`${item.cta.label}: ${item.title}`}
                >
                  {item.cta.label}
                </Link>
              </div>
            </article>
          ))}
        </div>
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
