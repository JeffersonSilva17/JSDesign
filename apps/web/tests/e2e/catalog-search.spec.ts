import { spawn, spawnSync, type ChildProcess } from 'node:child_process';

import { expect, test } from '@playwright/test';

test.beforeEach(async ({ page }) => {
  await page.addInitScript(() => window.sessionStorage.setItem('jsdesign:first-purchase-discount:v1:viewed', '1'));
});

test('busca inicial oferece formulário GET acessível e categorias sem consulta vazia', async ({ page }) => {
  await page.goto('/buscar');
  const search = page.getByRole('search');
  await expect(page.getByRole('heading', { level: 1, name: 'Buscar' })).toBeVisible();
  await expect(search.getByLabel('O que você procura?')).toBeVisible();
  await expect(search.getByRole('button', { name: 'Buscar produtos' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Comece por uma ideia' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Festas', exact: true })).toHaveAttribute('href', '/produtos?category=festas');
  await expect(page.getByRole('link', { name: 'Aniversário', exact: true })).toHaveAttribute('href', '/buscar?q=Anivers%C3%A1rio');
  await expect(page.getByText(/temporariamente indisponível|nenhum resultado/i)).toHaveCount(0);
});

test('renderiza exatos antes de semelhantes e preserva termo no detalhe e retorno', async ({ page }) => {
  await page.goto('/buscar?q=convite');
  await expect(page.getByRole('heading', { name: 'Resultados exatos' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Resultados semelhantes' })).toBeVisible();
  await expect(page.getByRole('article')).toHaveCount(4);
  const body = await page.locator('main').innerText();
  expect(body.indexOf('Resultados exatos')).toBeLessThan(body.indexOf('Resultados semelhantes'));

  await page.getByRole('link', { name: /^Ver detalhes:/ }).first().click();
  await page.getByRole('link', { name: 'Voltar aos produtos' }).click();
  await expect(page).toHaveURL('/buscar?q=convite');
});

test('diferencia vazio, intenção de convite, busca inválida e página fora do intervalo', async ({ page }) => {
  await page.goto('/buscar?q=zzzzzz');
  await expect(page.getByRole('heading', { name: 'Nenhum resultado encontrado' })).toBeVisible();
  await expect(page.getByText('zzzzzz', { exact: true })).toBeVisible();

  await page.goto('/buscar?q=tema+de+sereia+para+convite');
  await expect(page.getByRole('heading', { name: 'Seu tema pode virar um convite personalizado' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Descobrir convites digitais personalizados' })).toHaveAttribute('href', /#busca=/);

  await page.goto('/buscar?q=a');
  await expect(page.getByRole('heading', { name: 'Busca inválida' })).toBeVisible();
  await expect(page.getByLabel('O que você procura?')).toHaveAttribute('aria-invalid', 'true');

  await page.goto('/buscar?q=produto&page=99');
  await expect(page.getByRole('heading', { name: 'Página fora do intervalo' })).toBeVisible();
  await page.getByRole('link', { name: 'Voltar para a última página' }).click();
  await expect(page).toHaveURL(/q=produto/);
  await expect(page).toHaveURL(/page=2/);

  await page.goto('/buscar?q=zzzzzz&page=99');
  await expect(page.getByRole('heading', { name: 'Página fora do intervalo' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Nenhum resultado encontrado' })).toHaveCount(0);
});

test('paginação cruza a sequência total e personagem não verificado não influencia a saída', async ({ page }) => {
  await page.goto('/buscar?q=produto');
  await expect(page.getByRole('article')).toHaveCount(12);
  await page.getByRole('link', { name: 'Próxima página' }).click();
  await expect(page.getByRole('article')).toHaveCount(1);
  await expect(page).toHaveURL(/q=produto/);
  await expect(page).toHaveURL(/page=2/);

  await page.goto('/buscar?q=princesa+aurora');
  await expect(page.getByRole('article')).toHaveCount(1);
  await page.goto('/buscar?q=personagem+pendente');
  await expect(page.getByRole('heading', { name: 'Nenhum resultado encontrado' })).toBeVisible();
  await expect(page.getByRole('article')).toHaveCount(0);
});

test('tarefa essencial funciona por GET/SSR sem JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await page.goto('/buscar');
  await page.getByLabel('O que você procura?').fill('convite');
  await page.getByRole('button', { name: 'Buscar produtos' }).click();
  await expect(page).toHaveURL('/buscar?q=convite');
  await expect(page.getByRole('heading', { name: 'Resultados exatos' })).toBeVisible();
  await context.close();
});

test('não vaza o termo por referrer ou requisições de terceiros', async ({ page }) => {
  const thirdPartyRequests: string[] = [];
  page.on('request', (request) => {
    const url = new URL(request.url());
    const currentUrl = page.url() === 'about:blank' ? request.url() : page.url();
    if (url.origin !== new URL(currentUrl).origin) thirdPartyRequests.push(request.url());
  });
  await page.goto('/buscar?q=convite-secreto');
  await expect(page.locator('meta[name="referrer"]')).toHaveAttribute('content', 'strict-origin-when-cross-origin');
  expect(thirdPartyRequests.filter((url) => url.includes('convite-secreto'))).toEqual([]);
});

test('renderiza indisponibilidade sanitizada quando o upstream falha', async ({ page }) => {
  const server = await startIsolatedNextWithBrokenApi();
  try {
    await page.goto('http://127.0.0.1:3012/buscar?q=convite');
    await expect(page.getByRole('heading', { name: 'Busca temporariamente indisponível' })).toBeVisible();
    await expect(page.getByLabel('O que você procura?')).toHaveValue('convite');
  } finally {
    stopProcess(server);
  }
});

for (const width of [320, 420, 760, 1100]) {
  test(`busca preserva reflow, foco e alvo de toque em ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 800 });
    await page.goto('/buscar?q=convite');
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= document.documentElement.clientWidth)).toBe(true);
    const input = page.getByLabel('O que você procura?');
    await input.focus();
    await page.keyboard.press('Tab');
    await expect(page.getByRole('button', { name: 'Buscar produtos' })).toBeFocused();
    const box = await page.getByRole('button', { name: 'Buscar produtos' }).boundingBox();
    expect(box?.height).toBeGreaterThanOrEqual(44);
    expect(box?.width).toBeGreaterThanOrEqual(44);
  });
}

test('busca mantém evidência local de LCP, CLS e interação por viewport', async ({ browser }) => {
  for (const width of [320, 420, 760, 1100]) {
    const samples = [];
    for (let sample = 0; sample < 2; sample += 1) {
      const page = await browser.newPage({ viewport: { width, height: 800 } });
      await page.addInitScript(() => {
        window.sessionStorage.setItem('jsdesign:first-purchase-discount:v1:viewed', '1');
        const target = window as unknown as { __searchVitals: { lcp: number; cls: number } };
        target.__searchVitals = { lcp: 0, cls: 0 };
        try { new PerformanceObserver((list) => { const last = list.getEntries().at(-1); if (last) target.__searchVitals.lcp = last.startTime; }).observe({ type: 'largest-contentful-paint', buffered: true }); } catch {}
        try { new PerformanceObserver((list) => { for (const entry of list.getEntries() as PerformanceEntryList & ReadonlyArray<{ hadRecentInput?: boolean; value?: number }>) if (!entry.hadRecentInput) target.__searchVitals.cls += entry.value ?? 0; }).observe({ type: 'layout-shift', buffered: true }); } catch {}
      });
      await page.goto('/buscar');
      await page.getByLabel('O que você procura?').fill('convite');
      const started = Date.now();
      await page.getByRole('button', { name: 'Buscar produtos' }).click();
      await expect(page.getByRole('heading', { name: 'Resultados exatos' })).toBeVisible();
      const interactionMs = Date.now() - started;
      await page.waitForTimeout(500);
      samples.push({ ...(await page.evaluate(() => (window as unknown as { __searchVitals: { lcp: number; cls: number } }).__searchVitals)), interactionMs });
      await page.close();
    }
    expect(percentile75(samples.map((sample) => sample.lcp)), `${width}px LCP p75`).toBeGreaterThan(0);
    expect(percentile75(samples.map((sample) => sample.lcp)), `${width}px LCP p75`).toBeLessThanOrEqual(2500);
    expect(percentile75(samples.map((sample) => sample.cls)), `${width}px CLS p75`).toBeLessThanOrEqual(0.1);
    expect(percentile75(samples.map((sample) => sample.interactionMs)), `${width}px interação p75`).toBeLessThanOrEqual(1000);
  }
});

function percentile75(values: number[]): number {
  const sorted = [...values].sort((left, right) => left - right);
  return sorted[Math.ceil(sorted.length * 0.75) - 1] ?? 0;
}

async function startIsolatedNextWithBrokenApi(): Promise<ChildProcess> {
  const server = spawn(process.execPath, ['node_modules/next/dist/bin/next', 'start', '--hostname', '127.0.0.1', '--port', '3012'], {
    cwd: process.cwd(),
    env: { ...safeEnv(), API_INTERNAL_URL: 'http://127.0.0.1:65530', NODE_ENV: 'production' },
    stdio: 'ignore',
  });
  for (let attempt = 0; attempt < 80; attempt += 1) {
    try { await fetch('http://127.0.0.1:3012/buscar'); return server; } catch {}
    await new Promise((resolve) => setTimeout(resolve, 250));
  }
  stopProcess(server);
  throw new Error('isolated Next.js search test server did not start');
}

function stopProcess(child: ChildProcess): void {
  if (child.pid === undefined || child.killed) return;
  if (process.platform === 'win32') { spawnSync('taskkill.exe', ['/pid', String(child.pid), '/t', '/f'], { stdio: 'ignore' }); return; }
  child.kill();
}

function safeEnv(): Record<string, string> {
  const environment: Record<string, string> = {};
  for (const [key, value] of Object.entries(process.env)) if (key.length > 0 && !key.startsWith('=') && value !== undefined) environment[key] = value;
  return environment;
}
