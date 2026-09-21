import { execFileSync } from 'node:child_process';
import { copyFileSync, lstatSync, mkdirSync, mkdtempSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';

export function stageSource(prefixes = []) {
  const root = resolve(import.meta.dirname, '..');
  const tools = join(root, '.security-tools');
  mkdirSync(tools, { recursive: true });
  const staging = mkdtempSync(join(tools, 'source-'));
  const files = execFileSync('git', ['ls-files', '-z', '--cached', '--others', '--exclude-standard'], { cwd: root }).toString().split('\0').filter(Boolean);
  for (const file of new Set(files)) {
    if (prefixes.length && !prefixes.some((prefix) => file.startsWith(prefix))) continue;
    const source = join(root, file);
    try { if (!lstatSync(source).isFile()) continue; } catch { continue; }
    const target = join(staging, file);
    mkdirSync(dirname(target), { recursive: true });
    copyFileSync(source, target);
  }
  return { root, staging };
}
