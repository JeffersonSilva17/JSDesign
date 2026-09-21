import { defineConfig, devices } from '@playwright/test';
import { randomBytes } from 'node:crypto';
import { databaseEnv } from './tests/e2e/database-env';

process.env.SITEMAP_CLIENT_KEY ??= randomBytes(32).toString('hex');

export default defineConfig({
  testDir: './tests/e2e',
  globalSetup: './tests/e2e/global-setup.ts',
  timeout: 30_000,
  expect: {
    timeout: 5_000,
  },
  use: {
    baseURL: 'http://127.0.0.1:3000',
    trace: 'on-first-retry',
  },
  webServer: [
    {
      command: 'php artisan serve --host=127.0.0.1 --port=8000',
      cwd: '../api',
      url: 'http://127.0.0.1:8000/api/v1/health',
      reuseExistingServer: false,
      timeout: 120_000,
      env: {
        APP_ENV: 'testing', DB_CONNECTION: 'pgsql', DB_HOST: '127.0.0.1', DB_PORT: '5432',
        DB_DATABASE: 'jsdesign_test', ...databaseEnv('runtime'),
        SITEMAP_CLIENT_KEY: process.env.SITEMAP_CLIENT_KEY,
        CATALOG_PUBLIC_IMAGE_RESOLVER: 'e2e', CACHE_STORE: 'array', QUEUE_CONNECTION: 'sync', SESSION_DRIVER: 'array',
      },
    },
    {
      command: process.env.CI ? 'npm run start' : 'npm run dev',
      url: 'http://127.0.0.1:3000',
      reuseExistingServer: false,
      timeout: 120_000,
      env: { API_INTERNAL_URL: 'http://127.0.0.1:8000', SITE_URL: 'http://127.0.0.1:3000', SEO_INDEXING_ENABLED: process.env.SEO_INDEXING_ENABLED ?? 'false', SITEMAP_CLIENT_KEY: process.env.SITEMAP_CLIENT_KEY },
    },
  ],
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
