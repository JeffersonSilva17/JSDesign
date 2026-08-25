import { execFileSync } from 'node:child_process';
import { resolve } from 'node:path';

export default function globalSetup() {
  const api = resolve('..', 'api');
  const env = {
    ...process.env,
    APP_ENV: 'testing',
    DB_CONNECTION: 'pgsql',
    DB_HOST: '127.0.0.1',
    DB_PORT: '5432',
    DB_DATABASE: 'jsdesign_test',
    DB_USERNAME: 'jsdesign',
    DB_PASSWORD: 'jsdesign',
  };
  execFileSync('php', ['artisan', 'migrate:fresh', '--force'], { cwd: api, env, stdio: 'inherit' });
  execFileSync('php', ['artisan', 'db:seed', '--class=Database\\Seeders\\CatalogE2eSeeder', '--force'], { cwd: api, env, stdio: 'inherit' });
}
