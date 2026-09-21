import type { CatalogSeoContent } from './publicContent.types';

export const catalogSeoContent = {
  categoryDescription: (label: string) => `Explore ${label} no catálogo público da JS Designs.`,
  pageTitle: (title: string, page: number) => page > 1 ? `${title} — Página ${page}` : title,
  socialAlt: 'JS Designs — produtos físicos e digitais para celebrar',
  socialTitle: 'JS Designs',
  socialDescription: 'Produtos físicos e digitais para celebrar',
} as const satisfies CatalogSeoContent;
