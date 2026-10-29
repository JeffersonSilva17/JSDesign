import { readLimitedJsonBody } from './pricingValidation.ts';

export type CartConfiguration = Readonly<Record<string, string | boolean>>;
export type CartAddInput = Readonly<{
  product_slug: string; model_key: string; quantity: number; quote_snapshot_id: string; configuration: CartConfiguration;
}>;
export type CartItemResult = Readonly<{
  item_reference: string; product_id: string; product_slug: string; model_id: string; model_key: string; quantity: number;
  configuration: CartConfiguration; quote_snapshot_id: string; quote: Readonly<{ total_minor: number; currency: 'EUR'; pricing_rule_version: number }>;
  created_at: string;
}>;
export type CartErrorCode = 'invalid_request' | 'unsupported_mode' | 'invalid_configuration' | 'stale_quote' | 'unavailable' | 'idempotency_conflict' | 'csrf_failed' | 'rate_limited' | 'temporarily_unavailable';
export type CartError = Readonly<{ error: Readonly<{ code: CartErrorCode; message: string; correlation_id: string }> }>;
const codes: readonly CartErrorCode[] = ['invalid_request','unsupported_mode','invalid_configuration','stale_quote','unavailable','idempotency_conflict','csrf_failed','rate_limited','temporarily_unavailable'];

export const cartMessages: Readonly<Record<CartErrorCode, Readonly<Record<'pt-BR'|'en'|'es', string>>>> = {
  invalid_request: { 'pt-BR': 'Revise os dados informados.', en: 'Review the information provided.', es: 'Revisa la información proporcionada.' },
  unsupported_mode: { 'pt-BR': 'Este produto não pode ser adicionado por este fluxo.', en: 'This product cannot be added through this flow.', es: 'Este producto no se puede añadir mediante este flujo.' },
  invalid_configuration: { 'pt-BR': 'Revise os campos obrigatórios da personalização.', en: 'Review the required personalization fields.', es: 'Revisa los campos obligatorios de personalización.' },
  stale_quote: { 'pt-BR': 'O preço mudou. Confira a nova cotação e confirme novamente.', en: 'The price changed. Review the new quote and confirm again.', es: 'El precio cambió. Revisa el nuevo presupuesto y confirma de nuevo.' },
  unavailable: { 'pt-BR': 'Este produto ou modelo não está mais disponível.', en: 'This product or model is no longer available.', es: 'Este producto o modelo ya no está disponible.' },
  idempotency_conflict: { 'pt-BR': 'Esta tentativa já foi usada com outros dados. Envie novamente.', en: 'This submission key was used for different details. Submit again.', es: 'Esta clave ya se usó con otros datos. Envía de nuevo.' },
  csrf_failed: { 'pt-BR': 'Sua sessão precisa ser atualizada. Tente novamente.', en: 'Your session needs to be refreshed. Try again.', es: 'Tu sesión debe actualizarse. Inténtalo de nuevo.' },
  rate_limited: { 'pt-BR': 'Muitas tentativas. Aguarde um instante e tente novamente.', en: 'Too many submissions. Try again shortly.', es: 'Demasiados envíos. Inténtalo de nuevo pronto.' },
  temporarily_unavailable: { 'pt-BR': 'Não foi possível adicionar agora. Seus dados continuam preenchidos.', en: 'The item could not be added now. Your details are still here.', es: 'No se pudo añadir ahora. Tus datos siguen aquí.' },
};

export function parseCartInput(value: unknown): CartAddInput | null {
  if (!isRecord(value) || !exact(value, ['product_slug','model_key','quantity','quote_snapshot_id','configuration']) ||
    !slug(value.product_slug) || !slug(value.model_key) || !Number.isSafeInteger(value.quantity) || (value.quantity as number) < 1 || (value.quantity as number) > 10000 ||
    typeof value.quote_snapshot_id !== 'string' || !/^[a-f0-9]{64}$/.test(value.quote_snapshot_id) || !isCartConfiguration(value.configuration, value.model_key)) return null;
  return value as CartAddInput;
}

export function isCartItem(value: unknown): value is CartItemResult {
  if (!isRecord(value) || !exact(value, ['item_reference','product_id','product_slug','model_id','model_key','quantity','configuration','quote_snapshot_id','quote','created_at']) ||
    typeof value.item_reference !== 'string' || !uuid(value.item_reference) || !uuid(value.product_id) || !uuid(value.model_id) || !slug(value.product_slug) || !slug(value.model_key) ||
    !Number.isSafeInteger(value.quantity) || typeof value.quote_snapshot_id !== 'string' || !/^[a-f0-9]{64}$/.test(value.quote_snapshot_id) ||
    typeof value.created_at !== 'string' || Number.isNaN(Date.parse(value.created_at)) || !isCartConfiguration(value.configuration, value.model_key) || !isRecord(value.quote) ||
    !exact(value.quote, ['total_minor','currency','pricing_rule_version']) || !Number.isSafeInteger(value.quote.total_minor) || (value.quote.total_minor as number) < 0 ||
    value.quote.currency !== 'EUR' || !Number.isSafeInteger(value.quote.pricing_rule_version) || (value.quote.pricing_rule_version as number) < 1) return false;
  return true;
}

export function isCartError(value: unknown): value is CartError {
  return isRecord(value) && exact(value, ['error']) && isRecord(value.error) && exact(value.error, ['code','message','correlation_id']) &&
    codes.includes(value.error.code as CartErrorCode) && typeof value.error.message === 'string' && uuid(value.error.correlation_id);
}

export function isCartEnvelope(value: unknown): value is Readonly<{ items: readonly CartItemResult[] }> {
  return isRecord(value) && exact(value, ['items']) && Array.isArray(value.items) && value.items.every(isCartItem);
}

export { readLimitedJsonBody };
function isRecord(value: unknown): value is Record<string, unknown> { return value !== null && typeof value === 'object' && !Array.isArray(value); }
function exact(value: Record<string, unknown>, keys: readonly string[]): boolean { return Object.keys(value).length === keys.length && keys.every((key) => Object.hasOwn(value, key)); }
function slug(value: unknown): value is string { return typeof value === 'string' && value.length > 0 && value.length <= 180 && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value); }
function uuid(value: unknown): value is string { return typeof value === 'string' && /^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(value); }
function isCartConfiguration(value: unknown, modelKey: string): value is CartConfiguration {
  if (!isRecord(value) || typeof value.defer_briefing !== 'boolean' || !['none','client_file','create_by_js_designs'].includes(String(value.thumbnail_choice))) return false;
  const text = (key: string, maximum: number): boolean => typeof value[key] === 'string' && value[key].trim().length > 0 && value[key].length <= maximum;
  const date = typeof value.event_date === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value.event_date) && !Number.isNaN(Date.parse(`${value.event_date}T00:00:00Z`)) && new Date(`${value.event_date}T00:00:00Z`).toISOString().slice(0,10) === value.event_date;
  if (value.configuration_type === 'physical_personalized') {
    const keys = ['configuration_type','theme','personalization_text','event_date','thumbnail_choice','defer_briefing'];
    if (Object.hasOwn(value, 'note')) keys.push('note');
    return exact(value, keys) && text('theme',120) && text('personalization_text',120) && date &&
      (!Object.hasOwn(value, 'note') || (typeof value.note === 'string' && value.note.trim().length > 0 && value.note.length <= 500));
  }
  if (value.configuration_type === 'digital_personalized') {
    const keys = ['configuration_type','invite_type','theme','colors','event_name','event_date','event_time','event_location','thumbnail_choice','defer_briefing'];
    return exact(value, keys) && value.invite_type === modelKey && ['standard','complex'].includes(modelKey) && text('theme',120) && text('colors',160) &&
      text('event_name',120) && text('event_location',200) && date && typeof value.event_time === 'string' && /^(?:[01]\d|2[0-3]):[0-5]\d$/.test(value.event_time);
  }
  return false;
}
