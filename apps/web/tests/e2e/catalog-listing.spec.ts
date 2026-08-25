import { spawn, spawnSync, type ChildProcess } from 'node:child_process';

import { expect, test } from '@playwright/test';

test('lista modalidades, filtra, pagina e abre o detalhe publicado', async ({ page }) => {
  await page.addInitScript(() => window.sessionStorage.setItem('jsdesign:first-purchase-discount:v1:viewed', '1'));
  await page.goto('/produtos');
  await expect(page.getByRole('heading', { level: 1, name: 'Produtos' })).toBeVisible();
  await expect(page.getByRole('article')).toHaveCount(12);
  await expect(page.getByText('Produto físico personalizado').first()).toBeVisible();
  await expect(page.getByText('Produto digital personalizado').first()).toBeVisible();
  await expect(page.getByText('Download imediato').first()).toBeVisible();
  await expect(page.getByAltText('Produto digital em tons neutros')).toBeVisible();

  await page.getByRole('link', { name: 'Festas', exact: true }).click();
  await expect(page).toHaveURL(/category=festas/);
  await expect(page.getByRole('link', { name: '2', exact: true })).toBeVisible();
  await page.getByRole('link', { name: 'Próxima página' }).click();
  await expect(page).toHaveURL(/page=2/);
  await expect(page.getByRole('article')).toHaveCount(1);
  await page.getByRole('link', { name: /^Ver detalhes:/ }).first().click();
  await page.getByRole('link', { name: 'Voltar aos produtos' }).click();
  await expect(page).toHaveURL(/category=festas/);
  await expect(page).toHaveURL(/page=2/);

  await page.goto('/produtos');
  const details = page.getByRole('link', { name: /^Ver detalhes:/ }).first();
  await details.click();
  await expect(page.getByRole('heading', { level: 1 })).toHaveText(/Produto \d+/);
  await expect(page.getByRole('link', { name: 'Voltar aos produtos' })).toBeVisible();
});

test('rejeita filtro e slug malformados sem falso vazio ou falso 404 upstream', async ({ page }) => {
  await page.goto('/produtos?character=protegido');
  await expect(page.getByRole('heading', { name: 'Filtros inválidos' })).toBeVisible();
  const malformed = await page.goto('/produtos/%2E%2E%2Fsegredo');
  expect(malformed?.status()).toBe(404);
});

test('diferencia conjunto vazio de produto publicado inexistente', async ({ page }) => {
  await page.goto('/produtos?category=sem-produtos&page=99');
  await expect(page.getByRole('heading', { name: 'Nenhum produto encontrado' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Ver todos os produtos' })).toHaveAttribute('href', '/produtos?category=sem-produtos');
  await page.goto('/produtos?category=festas&page=99');
  await expect(page.getByRole('heading', { name: 'Página sem produtos' })).toBeVisible();
  await page.getByRole('link', { name: 'Voltar para a última página' }).click();
  await expect(page).toHaveURL(/category=festas/);
  await expect(page).toHaveURL(/page=2/);
  const missing = await page.goto('/produtos/produto-inexistente');
  expect(missing?.status()).toBe(404);
});

test('categorias expõe categorias, ocasiões e modalidades navegáveis', async ({ page }) => {
  await page.goto('/categorias');
  await expect(page.getByRole('link', { name: 'Festas', exact: true })).toHaveAttribute('href', '/produtos?category=festas');
  await expect(page.getByRole('link', { name: 'Aniversário', exact: true })).toHaveAttribute('href', '/produtos?occasion=aniversario');
  await expect(page.getByRole('link', { name: 'Produto digital', exact: true })).toHaveAttribute('href', '/produtos?modality=digital_ready');
});

test('listagem renderiza indisponibilidade sanitizada quando o upstream falha', async ({ page }) => {
  const server = await startIsolatedNextWithBrokenApi();
  try {
    await page.goto('http://127.0.0.1:3011/produtos');
    await expect(page.getByRole('heading', { name: 'Catálogo temporariamente indisponível' })).toBeVisible();
  } finally {
    stopProcess(server);
  }
});

for (const width of [320, 420, 760, 1100]) {
  test(`catálogo preserva reflow e alvos de toque em ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 800 });
    await page.goto('/produtos');
    await expect(page.getByRole('heading', { level: 1, name: 'Produtos' })).toBeVisible();
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= document.documentElement.clientWidth)).toBe(true);
    const box = await page.getByRole('link', { name: /^Ver detalhes:/ }).first().boundingBox();
    expect(box?.height).toBeGreaterThanOrEqual(44);
    expect(box?.width).toBeGreaterThanOrEqual(44);
  });
}

test('catálogo mantém evidência local de Core Web Vitals bons por viewport', async ({ browser }) => {
  const widths = [320, 420, 760, 1100];

  for (const width of widths) {
    const samples = [];
    for (let sample = 0; sample < 4; sample++) {
      const page = await browser.newPage({ viewport: { width, height: 800 } });
      await page.addInitScript(() => {
        window.sessionStorage.setItem('jsdesign:first-purchase-discount:v1:viewed', '1');
        const target = window as unknown as { __catalogVitals: { lcp: number; cls: number } };
        target.__catalogVitals = { lcp: 0, cls: 0 };
        try {
          new PerformanceObserver((list) => {
            const entries = list.getEntries();
            const last = entries.at(-1);
            if (last) target.__catalogVitals.lcp = last.startTime;
          }).observe({ type: 'largest-contentful-paint', buffered: true });
        } catch {}
        try {
          new PerformanceObserver((list) => {
            for (const entry of list.getEntries() as PerformanceEntryList & ReadonlyArray<{ hadRecentInput?: boolean; value?: number }>) {
              if (!entry.hadRecentInput) target.__catalogVitals.cls += entry.value ?? 0;
            }
          }).observe({ type: 'layout-shift', buffered: true });
        } catch {}
      });

      await page.goto('/produtos');
      await expect(page.getByRole('heading', { level: 1, name: 'Produtos' })).toBeVisible();
      const interactionStart = Date.now();
      await page.getByRole('link', { name: 'Festas', exact: true }).click();
      await expect(page).toHaveURL(/category=festas/);
      const interactionMs = Date.now() - interactionStart;
      await page.waitForTimeout(500);
      const vitals = await page.evaluate(() => (window as unknown as { __catalogVitals: { lcp: number; cls: number } }).__catalogVitals);
      samples.push({ ...vitals, interactionMs });
      await page.close();
    }

    expect(percentile75(samples.map((sample) => sample.lcp)), `${width}px LCP p75`).toBeGreaterThan(0);
    expect(percentile75(samples.map((sample) => sample.lcp)), `${width}px LCP p75`).toBeLessThanOrEqual(2500);
    expect(percentile75(samples.map((sample) => sample.cls)), `${width}px CLS p75`).toBeLessThanOrEqual(0.1);
    expect(percentile75(samples.map((sample) => sample.interactionMs)), `${width}px interaction p75`).toBeLessThanOrEqual(1000);
  }
});

function percentile75(values: number[]): number {
  const sorted = [...values].sort((left, right) => left - right);
  return sorted[Math.ceil(sorted.length * 0.75) - 1] ?? 0;
}

async function startIsolatedNextWithBrokenApi(): Promise<ChildProcess> {
  const server = spawn(process.execPath, ['node_modules/next/dist/bin/next', 'dev', '--hostname', '127.0.0.1', '--port', '3011'], {
    cwd: process.cwd(),
    env: {
      ...safeEnv(),
      API_INTERNAL_URL: 'http://127.0.0.1:65530',
      NEXT_DIST_DIR: '.next/broken-api-e2e',
      NODE_ENV: process.env.NODE_ENV ?? 'test',
    },
    stdio: 'ignore',
  });

  for (let attempt = 0; attempt < 80; attempt++) {
    try {
      await fetch('http://127.0.0.1:3011/produtos');
      return server;
    } catch {}
    await new Promise((resolve) => setTimeout(resolve, 250));
  }

  stopProcess(server);
  throw new Error('isolated Next.js test server did not start');
}

function stopProcess(child: ChildProcess): void {
  if (child.pid === undefined || child.killed) return;
  if (process.platform === 'win32') {
    spawnSync('taskkill.exe', ['/pid', String(child.pid), '/t', '/f'], { stdio: 'ignore' });
    return;
  }
  child.kill();
}

function safeEnv(): Record<string, string> {
  const environment: Record<string, string> = {};
  for (const [key, value] of Object.entries(process.env)) {
    if (key.length > 0 && !key.startsWith('=') && value !== undefined) {
      environment[key] = value;
    }
  }
  return environment;
}
