import { CatalogApiError } from './catalogErrors.ts';

type Fetcher = (input: URL, init: RequestInit) => Promise<Response>;

export async function fetchCatalogJson(
  url: URL,
  options: Readonly<{ detail?: boolean; timeoutMs?: number; fetcher?: Fetcher }> = {},
): Promise<unknown> {
  let response: Response;
  try {
    response = await (options.fetcher ?? fetch)(url, {
      headers: { accept: 'application/json' },
      cache: 'no-store',
      signal: AbortSignal.timeout(options.timeoutMs ?? 5_000),
    });
  } catch {
    throw new CatalogApiError('upstream-unavailable');
  }
  if (options.detail && response.status === 404) throw new CatalogApiError('not-found');
  if (!response.ok) throw new CatalogApiError('upstream-unavailable');
  try {
    return (await response.json()) as unknown;
  } catch {
    throw new CatalogApiError('invalid-payload');
  }
}
