import type { Metadata } from 'next';
import Link from 'next/link';
import { cookies } from 'next/headers';
import { readCart } from '@/bff/cartTransport';
import type { CartItemResult } from '@/bff/cartValidation';
import { getApiInternalUrl } from '@/bff/apiClient';
import type { SupportedLocale } from '@/i18n/locales';

export const metadata: Metadata = { title: 'Carrinho | JS Designs' };
const sessionCookie = '__Host-jsd_cart_session';
const labels: Readonly<Record<SupportedLocale, Readonly<Record<string,string>>>> = {
  'pt-BR': { title:'Carrinho', empty:'Seu carrinho está vazio.', unavailable:'Não foi possível carregar seu carrinho agora.', added:'Item adicionado ao carrinho.', model:'Modelo', quantity:'Quantidade', total:'Total', details:'Personalização', continue:'Continuar comprando' },
  en: { title:'Cart', empty:'Your cart is empty.', unavailable:'Your cart could not be loaded right now.', added:'Item added to your cart.', model:'Model', quantity:'Quantity', total:'Total', details:'Personalization', continue:'Continue shopping' },
  es: { title:'Carrito', empty:'Tu carrito está vacío.', unavailable:'No se pudo cargar tu carrito ahora.', added:'Artículo añadido al carrito.', model:'Modelo', quantity:'Cantidad', total:'Total', details:'Personalización', continue:'Seguir comprando' },
};

export default async function CartPage({ searchParams }: Readonly<{ searchParams: Promise<Record<string,string|string[]|undefined>> }>) {
  const cookieJar = await cookies();
  const locale = supportedLocale(cookieJar.get('jsd_locale')?.value);
  const session = cookieJar.get(sessionCookie)?.value;
  const token = process.env.API_CART_SERVICE_TOKEN ?? '';
  const items = !session ? [] : token ? await readCart(session, token, getApiInternalUrl()) : null;
  const query = await searchParams;
  const wasAdded = query.incluido === '1' && items !== null && items.length > 0;
  const copy = labels[locale];
  return <main className="cart-page">
    <h1>{copy.title}</h1>
    {wasAdded && <p role="status">{copy.added}</p>}
    {items === null ? <p role="alert">{copy.unavailable}</p> : items.length === 0 ? <p>{copy.empty}</p> : <ul className="cart-page__items">{items.map((item) => <CartItem key={item.item_reference} item={item} locale={locale} />)}</ul>}
    <Link className="button button--secondary" href="/produtos">{copy.continue}</Link>
  </main>;
}

function CartItem({ item, locale }: Readonly<{ item: CartItemResult; locale: SupportedLocale }>) {
  const copy = labels[locale];
  const config = item.configuration;
  const details = Object.entries(config).filter(([key, value]) => !['configuration_type','defer_briefing'].includes(key) && typeof value === 'string' && value !== 'none');
  const amount = BigInt(item.quote.total_minor);
  const fraction = (amount % 100n).toString().padStart(2, '0');
  const total = new Intl.NumberFormat(locale, { style:'currency', currency:'EUR' }).formatToParts(amount / 100n).map((part) => part.type === 'fraction' ? fraction : part.value).join('');
  return <li className="cart-page__item">
    <h2>{item.product_slug}</h2>
    <p>{copy.model}: {item.model_key}</p><p>{copy.quantity}: {item.quantity}</p><p>{copy.total}: {total}</p>
    <h3>{copy.details}</h3><dl>{details.map(([key,value]) => <div key={key}><dt>{humanize(key, locale)}</dt><dd>{String(value)}</dd></div>)}</dl>
  </li>;
}

function supportedLocale(value: string | undefined): SupportedLocale { return value === 'en' || value === 'es' ? value : 'pt-BR'; }
function humanize(key: string, locale: SupportedLocale): string {
  const names: Record<string,Record<string,string>> = {
    theme:{'pt-BR':'Tema',en:'Theme',es:'Tema'}, personalization_text:{'pt-BR':'Nome/texto principal',en:'Main name/text',es:'Nombre/texto principal'},
    colors:{'pt-BR':'Cores',en:'Colors',es:'Colores'}, event_name:{'pt-BR':'Nome/texto no convite',en:'Invitation text',es:'Texto de la invitación'},
    event_date:{'pt-BR':'Data',en:'Date',es:'Fecha'}, event_time:{'pt-BR':'Horário local',en:'Local time',es:'Hora local'},
    event_location:{'pt-BR':'Local',en:'Location',es:'Lugar'}, note:{'pt-BR':'Observação',en:'Note',es:'Nota'},
    thumbnail_choice:{'pt-BR':'Miniatura',en:'Thumbnail',es:'Miniatura'}, invite_type:{'pt-BR':'Tipo de convite',en:'Invitation type',es:'Tipo de invitación'},
  };
  return names[key]?.[locale] ?? key;
}
