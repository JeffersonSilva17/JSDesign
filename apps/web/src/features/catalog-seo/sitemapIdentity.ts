import { createHmac, timingSafeEqual } from 'node:crypto';
import { isIP } from 'node:net';

export const clientHeader = 'x-catalog-client';

function key(): string {
  const value = process.env.SITEMAP_CLIENT_KEY;
  if (!value || !/^[a-f0-9]{64}$/i.test(value)) throw new Error('Configuração de identidade do sitemap inválida.');
  return value;
}

function digest(message: string): string { return createHmac('sha256', key()).update(message).digest('hex'); }

export function clientAddress(remote: string | undefined, realIp: string | string[] | undefined): string {
  const address = remote?.replace(/^::ffff:/, '') ?? '';
  if (!isIP(address)) throw new Error('Identidade de conexão inválida.');
  const proxies = (process.env.CATALOG_TRUSTED_PROXY_IPS ?? '').split(',').map((ip) => ip.trim()).filter(Boolean);
  if (proxies.some((ip) => !isIP(ip))) throw new Error('Configuração de proxy inválida.');
  if (!proxies.includes(address)) return address;
  if (typeof realIp !== 'string' || !isIP(realIp)) throw new Error('Identidade de proxy inválida.');
  return realIp;
}

export function signClient(address: string, now = Math.floor(Date.now() / 1000)): string {
  const id = digest(`sitemap-ip:v1:${address}`);
  return `${id}.${now}.${digest(`sitemap-client:v1:${id}:${now}`)}`;
}

export function validClient(token: string, now = Math.floor(Date.now() / 1000)): boolean {
  const match = /^([a-f0-9]{64})\.([0-9]{10})\.([a-f0-9]{64})$/.exec(token);
  if (!match || Math.abs(now - Number(match[2])) > 30) return false;
  return timingSafeEqual(Buffer.from(match[3], 'hex'), Buffer.from(digest(`sitemap-client:v1:${match[1]}:${match[2]}`), 'hex'));
}
