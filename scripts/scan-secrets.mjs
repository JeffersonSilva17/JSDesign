import { spawnSync } from 'node:child_process';
import { stageSource } from './stage-security-source.mjs';

// Include uncommitted work; ignored environment files never leave their directory.
const { root, staging } = stageSource();
const image = 'zricethezav/gitleaks@sha256:c00b6bd0aeb3071cbcb79009cb16a60dd9e0a7c60e2be9ab65d25e6bc8abbb7f';
const result = spawnSync('docker', ['run', '--rm', '--network', 'none', '-v', `${staging}:/src:ro`, image, 'dir', '/src', '--redact', '--no-banner', '--config', '/src/.gitleaks.toml'], { cwd: root, stdio: 'inherit', windowsHide: true });
process.exitCode = result.status ?? 1;
