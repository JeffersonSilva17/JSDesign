export type CatalogErrorKind =
  | 'invalid-filter'
  | 'not-found'
  | 'upstream-unavailable'
  | 'invalid-payload';

export class CatalogApiError extends Error {
  readonly kind: CatalogErrorKind;

  constructor(kind: CatalogErrorKind) {
    super(kind);
    this.kind = kind;
    this.name = 'CatalogApiError';
  }
}
