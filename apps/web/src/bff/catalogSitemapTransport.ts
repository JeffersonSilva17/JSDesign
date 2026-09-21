export class SitemapError extends Error {
  readonly status: number;
  readonly retryAfter: string;
  constructor(status = 503, retryAfter = '60') {
    super('Sitemap indisponível.');
    this.status = status;
    this.retryAfter = retryAfter;
  }
}

export async function fetchSitemapJson(url: URL, limit: number, options: Readonly<{ fetcher?: typeof fetch; timeoutMs?: number; productChild?: boolean; clientToken?: string }> = {}): Promise<unknown> {
  const controller = new AbortController();
  let reader: ReadableStreamDefaultReader<Uint8Array> | undefined;
  let timer: ReturnType<typeof setTimeout> | undefined;
  const deadline = new Promise<never>((_, reject) => {
    timer = setTimeout(() => { controller.abort(); void reader?.cancel().catch(() => {}); reject(new SitemapError()); }, options.timeoutMs ?? 5000);
  });
  try {
    return await Promise.race([deadline, (async () => {
      const response = await (options.fetcher ?? fetch)(url, { cache: 'no-store', signal: controller.signal, headers: { Accept: 'application/json', ...(options.clientToken ? { 'X-Catalog-Client': options.clientToken } : {}) }, redirect: 'error' });
      if (response.status === 429) {
        const retry = response.headers.get('retry-after') ?? '';
        void response.body?.cancel().catch(() => {});
        throw new SitemapError(429, /^[1-9][0-9]*$/.test(retry) && Number(retry) <= 3600 ? retry : '60');
      }
      if (response.status === 404 && options.productChild) {
        void response.body?.cancel().catch(() => {});
        throw new SitemapError(404);
      }
      const length = response.headers.get('content-length');
      if ((length !== null && (!/^\d+$/.test(length) || Number(length) > limit)) || !/^application\/json(?:\s*;|$)/i.test(response.headers.get('content-type') ?? '') || !response.body) {
        void response.body?.cancel().catch(() => {});
        throw new SitemapError();
      }
      reader = response.body.getReader();
      const chunks: Uint8Array[] = [];
      let size = 0;
      while (true) {
        const chunk = await reader.read();
        if (chunk.done) break;
        size += chunk.value.byteLength;
        if (size > limit) { void reader.cancel().catch(() => {}); throw new SitemapError(); }
        chunks.push(chunk.value);
      }
      if (!response.ok) throw new SitemapError();
      const bytes = new Uint8Array(size);
      let offset = 0;
      for (const chunk of chunks) { bytes.set(chunk, offset); offset += chunk.byteLength; }
      return JSON.parse(new TextDecoder('utf-8', { fatal: true }).decode(bytes)) as unknown;
    })()]);
  } catch (error) {
    if (error instanceof SitemapError) throw error;
    throw new SitemapError();
  } finally {
    clearTimeout(timer);
    controller.abort();
  }
}
