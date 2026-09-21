import { expect, test } from '@playwright/test';

const enabled = process.env.SEO_INDEXING_ENABLED === 'true';
const origin = 'http://127.0.0.1:3000';

for (const bot of ['Twitterbot/1.0', 'facebookexternalhit/1.1', 'Slackbot-LinkExpanding 1.0']) {
  test(`HTML SSR completo para ${bot}`, async ({ request }) => {
    for (const [path, canonical, title] of [
      ['/produtos', '/produtos', 'Produtos'],
      ['/produtos?page=1', '/produtos', 'Produtos'],
      ['/produtos?category=festas&page=2', '/produtos?category=festas&page=2', 'Festas'],
      ['/categorias', '/categorias', 'Categorias'],
      ['/produtos/produto-1?return_to=%2Fbuscar%3Fq%3Dconvite', '/produtos/produto-1', 'Produto 1'],
    ]) {
      const response = await request.get(path, { headers: { 'user-agent': bot, 'x-forwarded-host': 'attacker.test' } });
      expect(response.status()).toBe(200);
      const html = await response.text();
      const head = html.split('</head>')[0];
      expect(head).toContain('lang="pt-BR"');
      expect(head.match(/<title>/g)).toHaveLength(1);
      expect(head).toContain(title);
      expect(head.match(/name="description"/g)).toHaveLength(1);
      expect(head).toContain('property="og:locale" content="pt_BR"');
      expect(head).toContain('name="twitter:card" content="summary_large_image"');
      for (const property of ['title', 'description', 'image', 'image:alt', 'site_name', 'type', 'locale']) expect(head.match(new RegExp(`property="og:${property}"`, 'g'))).toHaveLength(1);
      for (const property of ['title', 'description', 'image', 'image:alt', 'card']) expect(head.match(new RegExp(`name="twitter:${property}"`, 'g'))).toHaveLength(1);
      expect(head).not.toContain('attacker.test');
      expect(head).not.toContain('return_to');
      expect(head).not.toContain('q=convite');
      expect(head).toContain(enabled ? 'content="index, follow"' : 'content="noindex, nofollow"');
      if (enabled) {
        expect(head).toContain(`rel="canonical" href="${origin}${canonical.replace(/&/g, '&amp;')}"`);
        expect(head.match(/rel="canonical"/g)).toHaveLength(1);
        expect(head).toContain(`property="og:url" content="${origin}${canonical.replace(/&/g, '&amp;')}"`);
      }
      else {
        expect(head).not.toContain('rel="canonical"');
        expect(response.headers()['x-robots-tag']).toBe('noindex, nofollow');
        expect(html).not.toContain('type="application/ld+json"');
      }
      expect(html).not.toMatch(/private\/e2e-evidence|verification_status|storage_reference/);
    }
    for (const path of ['/produtos/missing', '/produtos/INVALID']) {
      const response = await request.get(path, { headers: { 'user-agent': bot } });
      expect(response.status()).toBe(404);
      const html = await response.text();
      expect(html).toContain('noindex');
      expect(html).not.toContain('rel="canonical"');
      expect(html).not.toContain('application/ld+json');
    }
  });
}

test('estados não indexáveis, busca privada e imagem editorial real', async ({ request }) => {
  for (const path of ['/buscar', '/buscar?q=termo-secreto', '/produtos?page=99', '/produtos?x=1', '/produtos?page=1&page=2', '/produtos/produto-1?return_to=https://evil.test']) {
    const response = await request.get(path, { headers: { 'user-agent': 'Twitterbot/1.0' } });
    const html = await response.text();
    const head = html.split('</head>')[0];
    expect(head).toContain('noindex');
    expect(head).not.toContain('rel="canonical"');
    expect(head).not.toContain('termo-secreto');
    expect(html).not.toContain('application/ld+json');
  }
  const image = await request.get('/catalog-social.png');
  expect(image.status()).toBe(200);
  expect(image.headers()['content-type']).toContain('image/png');
  const png = await image.body();
  expect(png.subarray(1, 4).toString()).toBe('PNG');
  expect(png.readUInt32BE(16)).toBe(1200);
  expect(png.readUInt32BE(20)).toBe(630);
});

test('sitemap percorre todos os filhos e respeita modo global', async ({ request, page }) => {
  const index = await request.get('/sitemap.xml');
  expect(index.status()).toBe(enabled ? 200 : 404);
  expect(index.headers()['cache-control']).toContain('no-store');
  const robots = await (await request.get('/robots.txt')).text();
  expect(robots.includes('Sitemap:')).toBe(enabled);
  if (!enabled) {
    for (const path of ['/sitemap-catalogo.xml?page=1', '/sitemap-editorial.xml', '/sitemap.xml?invalid=1']) expect((await request.get(path)).status()).toBe(404);
    return;
  }
  const xml = await index.text();
  const links = [...xml.matchAll(/<loc>(.*?)<\/loc>/g)].map((match) => match[1].replace(/&amp;/g, '&'));
  expect(links).toHaveLength(2);
  const products: string[] = [];
  for (const link of links) {
    const child = await request.get(link);
    expect(child.status()).toBe(200);
    expect(child.headers()['content-type']).toContain('application/xml');
    const body = await child.text();
    expect(await page.evaluate((body) => new DOMParser().parseFromString(body, 'application/xml').querySelector('parsererror') === null, body)).toBe(true);
    if (link.includes('catalogo')) products.push(...[...body.matchAll(/<loc>(.*?)<\/loc>/g)].map((match) => match[1]));
  }
  expect(new Set(products).size).toBe(13);
  for (const path of ['/sitemap-catalogo.xml', '/sitemap-catalogo.xml?page=01', '/sitemap-catalogo.xml?page=1&page=2', '/sitemap.xml?x=1']) expect((await request.get(path)).status()).toBe(422);
  expect((await request.get('/sitemap-catalogo.xml?page=2')).status()).toBe(404);
});

test('categoria e retorno funcionam sem JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await page.goto(`${origin}/produtos?category=festas`);
  await expect(page.getByRole('heading', { level: 1, name: 'Festas' })).toBeVisible();
  await page.getByRole('link', { name: /^Ver detalhes:/ }).first().click();
  await page.getByRole('link', { name: 'Voltar aos produtos' }).click();
  await expect(page).toHaveURL(/category=festas/);
  await context.close();
});
