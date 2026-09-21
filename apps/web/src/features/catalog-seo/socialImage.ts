import { closeSync, openSync, readSync } from 'node:fs';
import { join } from 'node:path';

import { isSafeCatalogImagePath } from '../../bff/catalogValidation.ts';

export function isSocialImage(path: string): boolean {
  if (!isSafeCatalogImagePath(path) || !/\.(png|jpe?g)$/i.test(path)) return false;
  let descriptor: number | undefined;
  try {
    descriptor = openSync(join(process.cwd(), 'public', decodeURIComponent(path).slice(1)), 'r');
    const bytes = Buffer.alloc(8);
    const length = readSync(descriptor, bytes, 0, 8, 0);
    return /\.png$/i.test(path)
      ? length === 8 && bytes.equals(Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]))
      : length >= 3 && bytes[0] === 255 && bytes[1] === 216 && bytes[2] === 255;
  } catch { return false; }
  finally { if (descriptor !== undefined) closeSync(descriptor); }
}
