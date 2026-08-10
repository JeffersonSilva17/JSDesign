import { expect, test } from '@playwright/test';

import {
  fetchFirstPurchaseOffer,
  requestFirstPurchaseCoupon,
} from '../../src/bff/apiClient';

const originalFetch = globalThis.fetch;
const originalApiUrl = process.env.API_INTERNAL_URL;

test.beforeEach(() => {
  process.env.API_INTERNAL_URL = 'http://laravel.internal';
});

test.afterEach(() => {
  globalThis.fetch = originalFetch;
  process.env.API_INTERNAL_URL = originalApiUrl;
});

test('BFF valida oferta completa e exige no-store', async () => {
  let capturedInit: RequestInit | undefined;
  globalThis.fetch = async (_input, init) => {
    capturedInit = init;

    return Response.json({
      enabled: true,
      delivery_mode: 'display',
      discount_percent: 10,
      minimum_amount: null,
      non_cumulative: true,
      manual_checkout_required: true,
      authorization_text_version: 'coupon-v1',
      offer_id: 'offer-hash',
      texts: {
        title: 'Primeira compra',
        description: 'Descrição aprovada.',
        authorization: 'Autorização específica.',
        privacy_url: '/privacidade',
      },
    });
  };

  await expect(fetchFirstPurchaseOffer()).resolves.toMatchObject({ enabled: true });
  expect(capturedInit?.cache).toBe('no-store');
  expect(capturedInit?.signal).toBeInstanceOf(AbortSignal);
});

test('BFF rejeita oferta incompleta e URL de privacidade externa', async () => {
  globalThis.fetch = async () =>
    Response.json({
      enabled: true,
      delivery_mode: 'display',
      discount_percent: 10,
      minimum_amount: null,
      non_cumulative: true,
      manual_checkout_required: true,
      authorization_text_version: 'coupon-v1',
      offer_id: 'offer-hash',
      texts: {
        title: '',
        description: 'Descrição.',
        authorization: 'Autorização.',
        privacy_url: 'https://example.com/privacidade',
      },
    });

  await expect(fetchFirstPurchaseOffer()).rejects.toThrow(/payload inválido/i);
});

test('BFF envia payload mínimo e propaga somente headers confiáveis já sanitizados', async () => {
  let sentBody = '';
  let sentHeaders: HeadersInit | undefined;
  globalThis.fetch = async (_input, init) => {
    sentBody = String(init?.body);
    sentHeaders = init?.headers;

    return Response.json(
      {
        status: 'accepted',
        delivery: 'display',
        message: 'Cupom emitido.',
        request_id: 'request-1',
        coupon_code: 'JS-ABCDEFGH23456789',
      },
      { status: 200 },
    );
  };

  await requestFirstPurchaseCoupon({
    email: 'cliente@example.com',
    authorizationTextVersion: 'coupon-v1',
    offerId: 'offer-hash',
    forwardingHeaders: { 'x-forwarded-for': '203.0.113.10' },
  });

  expect(JSON.parse(sentBody)).toEqual({
    email: 'cliente@example.com',
    authorization_accepted: true,
    authorization_text_version: 'coupon-v1',
    offer_id: 'offer-hash',
  });
  expect(new Headers(sentHeaders).get('x-forwarded-for')).toBe('203.0.113.10');
});

test('BFF preserva 422 somente para campos públicos permitidos', async () => {
  globalThis.fetch = async () =>
    Response.json(
      {
        message: 'Os dados são inválidos.',
        errors: {
          email: ['Informe um e-mail válido.'],
          internal_url: ['segredo'],
        },
      },
      { status: 422 },
    );

  const result = await requestFirstPurchaseCoupon({
    email: 'invalido',
    authorizationTextVersion: 'coupon-v1',
    offerId: 'offer-hash',
  });

  expect(result.status).toBe(422);
  expect(result.payload).toMatchObject({
    status: 'validation_error',
    errors: { email: ['Informe um e-mail válido.'] },
  });
  expect(JSON.stringify(result.payload)).not.toContain('internal_url');
});

test('BFF rejeita coupon_code em resposta de produção por e-mail', async () => {
  globalThis.fetch = async () =>
    Response.json(
      {
        status: 'accepted',
        delivery: 'email',
        message: 'Solicitação aceita.',
        request_id: 'request-1',
        coupon_code: 'NAO-PODE-VAZAR',
      },
      { status: 202 },
    );

  await expect(
    requestFirstPurchaseCoupon({
      email: 'cliente@example.com',
      authorizationTextVersion: 'coupon-v1',
      offerId: 'offer-hash',
    }),
  ).rejects.toThrow(/payload inválido/i);
});
