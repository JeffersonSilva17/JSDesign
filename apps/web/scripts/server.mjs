import { createServer } from 'node:http';

import nextEnv from '@next/env';
import next from 'next';

import { clientAddress, signClient } from '../src/features/catalog-seo/sitemapIdentity.ts';

const dev = process.argv.includes('--dev');
process.env.NODE_ENV = dev ? 'development' : 'production';
nextEnv.loadEnvConfig(process.cwd(), dev);
const port = Number(process.env.PORT ?? 3000);
const hostname = process.env.HOSTNAME_BIND ?? '127.0.0.1';
// Fail at startup rather than accepting requests without a trusted client identity.
signClient('127.0.0.1');
const server = createServer();
const app = next({ dev, hostname, port, httpServer: server });
await app.prepare();
const handle = app.getRequestHandler();
server.on('request', (req, res) => {
  delete req.headers['x-catalog-client'];
  try {
    req.headers['x-catalog-client'] = signClient(clientAddress(req.socket.remoteAddress, req.headers['x-real-ip']));
  } catch {
    res.writeHead(503, { 'Content-Type': 'application/json', 'Cache-Control': 'no-store' });
    res.end(JSON.stringify({ message: 'Serviço temporariamente indisponível.' }));
    return;
  }
  void handle(req, res);
});
server.on('upgrade', app.getUpgradeHandler());
server.listen(port, hostname, () => console.log(`Servidor web pronto na porta ${port}.`));
