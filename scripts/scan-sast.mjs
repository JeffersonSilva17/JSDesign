import { spawnSync } from 'node:child_process';
import { stageSource } from './stage-security-source.mjs';

const prefixes = ['apps/api/app/', 'apps/api/config/', 'apps/api/routes/', 'apps/api/database/', 'apps/api/scripts/', 'apps/api/tests/', 'apps/web/src/', 'apps/web/scripts/', 'apps/web/tests/', 'scripts/'];
const { root, staging } = stageSource(prefixes);
const image = 'semgrep/semgrep@sha256:34ab619bf1391a24bfda3f05debd0d8a6ce3093c2d5f9d39cfc00f83c1397823';
const result = spawnSync('docker', ['run', '--rm', '-v', `${staging}:/src:ro`, '-w', '/src', image, 'semgrep', 'scan', '--config', 'p/default', '--metrics=off', '--disable-version-check', '--no-git-ignore', '--error', ...prefixes], { cwd: root, stdio: 'inherit', windowsHide: true });
process.exitCode = result.status ?? 1;
