import { activeLocale, defaultLocale, supportedLocales, type SupportedLocale } from '@/i18n/locales';
import { publicContent, type PlaceholderPageKey } from '@/i18n/publicContent';

export { activeLocale, defaultLocale, publicContent, supportedLocales };
export type { PlaceholderPageKey, SupportedLocale };

export const mainNavItems = publicContent.navigation.mainNavItems;
export const publicCta = publicContent.cta.primary;
export const secondaryPublicCta = publicContent.cta.secondary;
export const footerSections = publicContent.footer.sections;
export const placeholderPages = publicContent.placeholders;
export const placeholderFallbackCtas = publicContent.placeholderFallbackCtas;
