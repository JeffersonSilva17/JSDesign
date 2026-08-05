export const defaultLocale = 'pt-BR';

export const supportedLocales = ['pt-BR', 'en', 'es'] as const;

export type SupportedLocale = (typeof supportedLocales)[number];

export const activeLocale: SupportedLocale = defaultLocale;

