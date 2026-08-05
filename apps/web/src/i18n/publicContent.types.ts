import type { SupportedLocale } from './locales';

type LinkContent = Readonly<{
  label: string;
  href: string;
  description?: string;
}>;

type MetadataContent = Readonly<{
  title: string;
  description: string;
  robots?: Readonly<{
    follow: boolean;
    index: boolean;
  }>;
}>;

export type PlaceholderContent = Readonly<{
  title: string;
  eyebrow: string;
  description: string;
  metadata: MetadataContent;
}>;

export type PublicContent = Readonly<{
  locale: SupportedLocale;
  brand: Readonly<{
    name: string;
    homeAriaLabel: string;
    shortSubtitle: string;
  }>;
  metadata: Readonly<{
    root: MetadataContent;
    home: MetadataContent;
  }>;
  navigation: Readonly<{
    ariaLabel: string;
    mobileLabel: string;
    skipToMainContent: string;
    mainNavItems: readonly LinkContent[];
  }>;
  cta: Readonly<{
    primary: LinkContent;
    secondary: LinkContent;
  }>;
  home: Readonly<{
    hero: Readonly<{
      eyebrow: string;
      title: string;
      lead: string;
      visualAriaLabel: string;
    }>;
    modalityCards: readonly Readonly<{
      label: string;
      title: string;
      description: string;
      tone?: 'accent';
    }>[];
    nextPaths: Readonly<{
      eyebrow: string;
      title: string;
      description: string;
    }>;
  }>;
  footer: Readonly<{
    eyebrow: string;
    title: string;
    description: string;
    support: string;
    trustCopy: string;
    navAriaLabel: string;
    copyright: string;
    sections: readonly Readonly<{
      id: string;
      title: string;
      links: readonly LinkContent[];
    }>[];
  }>;
  placeholders: Readonly<Record<string, PlaceholderContent>>;
  placeholderFallbackCtas: Readonly<{
    produtos: LinkContent;
    default: LinkContent;
  }>;
  qualityCopy: readonly string[];
}>;
