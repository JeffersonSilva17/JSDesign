export function parseSeoConfig(siteUrl: string | undefined, enabled = 'false') {
  let url: URL;
  try {
    if (!siteUrl || !/^https?:\/\/[^/?#\\\s]+\/?$/u.test(siteUrl)) throw new Error();
    url = new URL(siteUrl);
    const loopback = ['localhost', '127.0.0.1', '[::1]'].includes(url.hostname);
    if (url.username || url.password || url.pathname !== '/' || url.search || url.hash ||
        /[?#]/u.test(siteUrl) || (url.protocol !== 'https:' && !(url.protocol === 'http:' && loopback))) throw new Error();
  } catch {
    throw new Error('Configuração SITE_URL inválida.');
  }
  if (enabled !== 'true' && enabled !== 'false') throw new Error('Configuração SEO_INDEXING_ENABLED inválida.');
  return { origin: url.origin, indexing: enabled === 'true' } as const;
}
