import Link from 'next/link';

import { searchContent } from '@/features/public-store/publicLayoutContent';

export function SearchSuggestions({ suggestions }: Readonly<{ suggestions: readonly Readonly<{ label: string; href: string }>[] }>) {
  if (suggestions.length === 0) return null;
  return (
    <section className="catalog-search-suggestions" aria-labelledby="catalog-search-suggestions-title">
      <h2 id="catalog-search-suggestions-title">{searchContent.suggestions.title}</h2>
      <div className="catalog-search-chips">
        {suggestions.map((suggestion) => <Link key={suggestion.href} href={suggestion.href}>{suggestion.label}</Link>)}
      </div>
    </section>
  );
}
