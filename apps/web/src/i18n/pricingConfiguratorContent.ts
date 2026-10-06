import type { SupportedLocale } from './locales';

type PricingConfiguratorContent = Readonly<{
  quantity: string;
  quote: string;
  unit: string;
  subtotal: string;
  discount: string;
  total: string;
  pending: string;
  unavailable: string;
  retry: string;
  invalid: string;
  min: string;
  max: string;
  currency: string;
  cta: string;
  handoff: string;
}>;

export const pricingConfiguratorContent: Readonly<Record<SupportedLocale, PricingConfiguratorContent>> = {
  'pt-BR': {
    quantity: 'Quantidade', quote: 'Resumo do preço', unit: 'Preço unitário aplicável', subtotal: 'Subtotal antes do desconto',
    discount: 'Desconto', total: 'Total', pending: 'Calculando o preço…', unavailable: 'Não foi possível calcular o preço agora. Tente novamente.',
    retry: 'Tentar novamente', invalid: 'Informe um número inteiro dentro dos limites exibidos.', min: 'Mínimo', max: 'Máximo',
    currency: 'Moeda', cta: 'Continuar para configuração', handoff: 'Ainda não adicionamos este item ao carrinho.',
  },
  en: {
    quantity: 'Quantity', quote: 'Price summary', unit: 'Applicable unit price', subtotal: 'Subtotal before discount',
    discount: 'Discount', total: 'Total', pending: 'Calculating price…', unavailable: 'The price could not be calculated now. Try again.',
    retry: 'Try again', invalid: 'Enter a whole number within the displayed limits.', min: 'Minimum', max: 'Maximum',
    currency: 'Currency', cta: 'Continue to configuration', handoff: 'This item has not been added to the cart.',
  },
  es: {
    quantity: 'Cantidad', quote: 'Resumen del precio', unit: 'Precio unitario aplicable', subtotal: 'Subtotal antes del descuento',
    discount: 'Descuento', total: 'Total', pending: 'Calculando el precio…', unavailable: 'No se pudo calcular el precio ahora. Inténtalo de nuevo.',
    retry: 'Intentar de nuevo', invalid: 'Indica un número entero dentro de los límites mostrados.', min: 'Mínimo', max: 'Máximo',
    currency: 'Moneda', cta: 'Continuar a la configuración', handoff: 'Este artículo todavía no se ha añadido al carrito.',
  },
};
