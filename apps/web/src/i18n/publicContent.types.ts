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

type EditorialMostWantedItem = Readonly<{
  title: string;
  modality: string;
  description: string;
  visualLabel: string;
  tone: 'soft' | 'champagne' | 'calm';
  cta: LinkContent;
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
    }>;
    mostWanted: Readonly<{
      eyebrow: string;
      title: string;
      description: string;
      items: readonly EditorialMostWantedItem[];
    }>;
    nextPaths: Readonly<{
      eyebrow: string;
      title: string;
      description: string;
    }>;
  }>;
  catalog: Readonly<{
    listing: Readonly<{ eyebrow: string; title: string; description: string; emptyTitle: string; emptyDescription: string }>;
    categories: Readonly<{ eyebrow: string; title: string; description: string; empty: string }>;
    filters: Readonly<{ title: string; clear: string; category: string; occasion: string; modality: string }>;
    card: Readonly<{ details: string; unavailableImage: string; immediate: string; leadTime: string }>;
    pagination: Readonly<{ label: string; previous: string; next: string }>;
    states: Readonly<{ invalidTitle: string; invalidDescription: string; unavailableTitle: string; unavailableDescription: string; pageOutOfRangeTitle: string; pageOutOfRangeDescription: string; back: string; backToLastPage: string }>;
    detail: Readonly<{ eyebrow: string; back: string }>;
    modality: Readonly<Record<'physical_personalized' | 'digital_personalized' | 'digital_ready', string>>;
    availability: Readonly<Record<'available' | 'unavailable' | 'made_to_order', string>>;
    metadata: Readonly<{ products: MetadataContent; categories: MetadataContent; detail: MetadataContent }>;
  }>;
  search: Readonly<{
    metadata: MetadataContent;
    eyebrow: string;
    title: string;
    description: string;
    form: Readonly<{ label: string; placeholder: string; submit: string; help: string; error: string }>;
    initial: Readonly<{ title: string; description: string; categories: string; editorialSuggestions: string }>;
    exact: Readonly<{ title: string; categoryPrefix: string }>;
    similar: Readonly<{ title: string; description: string }>;
    suggestions: Readonly<{ title: string }>;
    termLabel: string;
    invitation: Readonly<{ title: string; description: string; action: string }>;
    states: Readonly<{
      emptyTitle: string;
      emptyDescription: string;
      invalidTitle: string;
      invalidDescription: string;
      unavailableTitle: string;
      unavailableDescription: string;
      outOfRangeTitle: string;
      outOfRangeDescription: string;
      retry: string;
      lastPage: string;
    }>;
    pagination: Readonly<{ label: string; previous: string; next: string }>;
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
    default: LinkContent;
  }>;
  firstPurchaseDiscount: Readonly<{
    fieldLabel: string;
    fieldHelp: string;
    manualUse: string;
    submit: string;
    submitting: string;
    close: string;
    decline: string;
    copy: string;
    copied: string;
    confirm: string;
    statusLabel: string;
    errors: Readonly<{
      emailRequired: string;
      emailInvalid: string;
      authorizationRequired: string;
      unavailable: string;
      retry: string;
    }>;
  }>;
  qualityCopy: readonly string[];
}>;
