export type ApiHealthPayload = {
  status: 'ok';
  service: 'jsdesign-api';
  api_version: 'v1';
  checks: {
    app: 'ok';
    database: 'ok';
    redis: 'ok';
  };
};

const API_HEALTH_TIMEOUT_MS = 5_000;

export function getApiInternalUrl(): string {
  const rawUrl = process.env.API_INTERNAL_URL?.trim();

  if (!rawUrl) {
    throw new Error('API_INTERNAL_URL não configurada no ambiente server-side.');
  }

  let apiUrl: URL;

  try {
    apiUrl = new URL(rawUrl);
  } catch {
    throw new Error('API_INTERNAL_URL inválida no ambiente server-side.');
  }

  if (apiUrl.protocol !== 'http:' && apiUrl.protocol !== 'https:') {
    throw new Error('API_INTERNAL_URL deve usar HTTP ou HTTPS.');
  }

  apiUrl.hash = '';
  apiUrl.search = '';

  return apiUrl.toString().replace(/\/$/, '');
}

function isApiHealthPayload(payload: unknown): payload is ApiHealthPayload {
  if (!payload || typeof payload !== 'object') {
    return false;
  }

  const candidate = payload as Partial<ApiHealthPayload>;

  return (
    candidate.status === 'ok' &&
    candidate.service === 'jsdesign-api' &&
    candidate.api_version === 'v1' &&
    candidate.checks?.app === 'ok' &&
    candidate.checks.database === 'ok' &&
    candidate.checks.redis === 'ok'
  );
}

export async function fetchApiHealth(): Promise<ApiHealthPayload> {
  const response = await fetch(`${getApiInternalUrl()}/api/v1/health`, {
    headers: {
      accept: 'application/json',
    },
    cache: 'no-store',
    signal: AbortSignal.timeout(API_HEALTH_TIMEOUT_MS),
  });

  if (!response.ok) {
    throw new Error(`Laravel API health respondeu HTTP ${response.status}.`);
  }

  const payload: unknown = await response.json();

  if (!isApiHealthPayload(payload)) {
    throw new Error('Laravel API health retornou payload inválido.');
  }

  return payload;
}
