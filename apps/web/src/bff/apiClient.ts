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

export type FirstPurchaseOfferPayload = {
  enabled: boolean;
  delivery_mode: 'display' | 'email';
  discount_percent: number;
  minimum_amount: number | null;
  non_cumulative: boolean;
  manual_checkout_required: boolean;
  authorization_text_version: string;
  offer_id: string;
  texts?: {
    title: string;
    description: string;
    authorization: string;
    privacy_url: string;
  };
};

type ValidationField =
  | 'email'
  | 'authorization_accepted'
  | 'authorization_text_version'
  | 'offer_id';

export type FirstPurchaseCouponPayload =
  | {
      status: 'accepted';
      delivery: 'display';
      message: string;
      request_id: string;
      coupon_code: string;
    }
  | {
      status: 'accepted';
      delivery: 'email';
      message: string;
      request_id: string;
      coupon_code?: never;
    }
  | {
      status: 'unavailable' | 'retry_later';
      delivery: 'none';
      message: string;
      request_id?: never;
      coupon_code?: never;
    }
  | {
      status: 'validation_error';
      delivery: 'none';
      message: string;
      errors: Partial<Record<ValidationField, string[]>>;
      request_id?: never;
      coupon_code?: never;
    };

const API_HEALTH_TIMEOUT_MS = 5_000;
const PROMOTION_TIMEOUT_MS = 5_000;

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

function isFirstPurchaseOfferPayload(payload: unknown): payload is FirstPurchaseOfferPayload {
  if (!payload || typeof payload !== 'object') {
    return false;
  }

  const candidate = payload as Partial<FirstPurchaseOfferPayload>;

  if (candidate.enabled === false) {
    return true;
  }

  return (
    candidate.enabled === true &&
    (candidate.delivery_mode === 'display' || candidate.delivery_mode === 'email') &&
    typeof candidate.discount_percent === 'number' &&
    candidate.discount_percent > 0 &&
    candidate.discount_percent <= 100 &&
    (candidate.minimum_amount === null ||
      (typeof candidate.minimum_amount === 'number' && candidate.minimum_amount >= 0)) &&
    typeof candidate.non_cumulative === 'boolean' &&
    typeof candidate.manual_checkout_required === 'boolean' &&
    isNonEmptyString(candidate.authorization_text_version) &&
    isNonEmptyString(candidate.offer_id) &&
    isNonEmptyString(candidate.texts?.title) &&
    isNonEmptyString(candidate.texts.description) &&
    isNonEmptyString(candidate.texts.authorization) &&
    isSameOriginPath(candidate.texts.privacy_url)
  );
}

function isNonEmptyString(value: unknown): value is string {
  return typeof value === 'string' && value.trim().length > 0;
}

function isSameOriginPath(value: unknown): value is string {
  return isNonEmptyString(value) && value.startsWith('/') && !value.startsWith('//');
}

function isFirstPurchaseCouponPayload(
  payload: unknown,
  responseStatus: number,
): payload is FirstPurchaseCouponPayload {
  if (!payload || typeof payload !== 'object') {
    return false;
  }

  const candidate = payload as Partial<FirstPurchaseCouponPayload>;

  if (!isNonEmptyString(candidate.message)) {
    return false;
  }

  if (candidate.status === 'accepted' && candidate.delivery === 'display') {
    return (
      responseStatus === 200 &&
      isNonEmptyString(candidate.request_id) &&
      isNonEmptyString(candidate.coupon_code)
    );
  }

  if (candidate.status === 'accepted' && candidate.delivery === 'email') {
    return (
      responseStatus === 202 &&
      isNonEmptyString(candidate.request_id) &&
      candidate.coupon_code === undefined
    );
  }

  return (
    (candidate.status === 'unavailable' || candidate.status === 'retry_later') &&
    candidate.delivery === 'none' &&
    candidate.request_id === undefined &&
    candidate.coupon_code === undefined &&
    ((candidate.status === 'unavailable' && responseStatus === 503) ||
      (candidate.status === 'retry_later' && responseStatus === 429))
  );
}

function validationPayload(payload: unknown): FirstPurchaseCouponPayload | null {
  if (!payload || typeof payload !== 'object') {
    return null;
  }

  const candidate = payload as { message?: unknown; errors?: unknown };

  if (!isNonEmptyString(candidate.message) || !candidate.errors || typeof candidate.errors !== 'object') {
    return null;
  }

  const allowedFields: ValidationField[] = [
    'email',
    'authorization_accepted',
    'authorization_text_version',
    'offer_id',
  ];
  const errors: Partial<Record<ValidationField, string[]>> = {};

  for (const field of allowedFields) {
    const messages = (candidate.errors as Record<string, unknown>)[field];

    if (Array.isArray(messages) && messages.every(isNonEmptyString)) {
      errors[field] = messages;
    }
  }

  return {
    status: 'validation_error',
    delivery: 'none',
    message: candidate.message,
    errors,
  };
}

export async function fetchFirstPurchaseOffer(): Promise<FirstPurchaseOfferPayload> {
  const response = await fetch(`${getApiInternalUrl()}/api/v1/promotions/first-purchase-offer`, {
    headers: {
      accept: 'application/json',
    },
    cache: 'no-store',
    signal: AbortSignal.timeout(PROMOTION_TIMEOUT_MS),
  });

  if (!response.ok) {
    throw new Error(`Laravel API promotion offer respondeu HTTP ${response.status}.`);
  }

  const payload: unknown = await response.json();

  if (!isFirstPurchaseOfferPayload(payload)) {
    throw new Error('Laravel API promotion offer retornou payload inválido.');
  }

  return payload;
}

export async function requestFirstPurchaseCoupon(input: {
  email: string;
  authorizationTextVersion: string;
  offerId: string;
  forwardingHeaders?: Readonly<Record<string, string>>;
}): Promise<{ payload: FirstPurchaseCouponPayload; status: number }> {
  const response = await fetch(`${getApiInternalUrl()}/api/v1/promotions/first-purchase-coupons`, {
    method: 'POST',
    headers: {
      accept: 'application/json',
      'content-type': 'application/json',
      ...input.forwardingHeaders,
    },
    cache: 'no-store',
    signal: AbortSignal.timeout(PROMOTION_TIMEOUT_MS),
    body: JSON.stringify({
      email: input.email,
      authorization_accepted: true,
      authorization_text_version: input.authorizationTextVersion,
      offer_id: input.offerId,
    }),
  });

  const payload: unknown = await response.json();

  if (response.status === 422) {
    const mappedValidation = validationPayload(payload);

    if (!mappedValidation) {
      throw new Error('Laravel API promotion request retornou validação inválida.');
    }

    return { payload: mappedValidation, status: 422 };
  }

  if (!isFirstPurchaseCouponPayload(payload, response.status)) {
    throw new Error('Laravel API promotion request retornou payload inválido.');
  }

  return { payload, status: response.status };
}
