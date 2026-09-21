import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { parseEnv } from 'node:util';

export function databaseEnv(role: 'runtime' | 'migrator') {
  const file = role === 'runtime' ? '.env' : '.env.testing';
  const env = parseEnv(readFileSync(resolve('..', 'api', file), 'utf8'));
  if (env.DB_USERNAME !== `jsdesign_${role}` || !env.DB_PASSWORD) throw new Error('Provision the isolated database roles before E2E.');
  return { DB_USERNAME: env.DB_USERNAME, DB_PASSWORD: env.DB_PASSWORD };
}
