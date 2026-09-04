import { searchContent } from '@/features/public-store/publicLayoutContent';

export function SearchForm({ query = '', invalid = false }: Readonly<{ query?: string; invalid?: boolean }>) {
  return (
    <form className="catalog-search-form" role="search" method="get" action="/buscar">
      <label htmlFor="catalog-search-query">{searchContent.form.label}</label>
      <div className="catalog-search-form__controls">
        <input
          id="catalog-search-query"
          name="q"
          type="search"
          defaultValue={query}
          placeholder={searchContent.form.placeholder}
          minLength={2}
          required
          aria-describedby={invalid ? 'catalog-search-help catalog-search-error' : 'catalog-search-help'}
          aria-invalid={invalid || undefined}
        />
        <button className="button button--primary" type="submit">{searchContent.form.submit}</button>
      </div>
      <p id="catalog-search-help" className="catalog-search-form__help">{searchContent.form.help}</p>
      {invalid && <p id="catalog-search-error" className="catalog-search-form__error" role="alert">{searchContent.form.error}</p>}
    </form>
  );
}
