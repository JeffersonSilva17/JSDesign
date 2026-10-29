import assert from 'node:assert/strict';
import test from 'node:test';
import { isCartError, isCartItem, parseCartInput } from '../../src/bff/cartValidation.ts';

test('cart input is closed and rejects client supplied customer or price data', () => {
  const valid = { product_slug:'convite', model_key:'standard', quantity:2, quote_snapshot_id:'a'.repeat(64), configuration:{configuration_type:'digital_personalized', invite_type:'standard', theme:'Jardim', colors:'Azul e branco', event_name:'Lia', event_date:'2027-04-12', event_time:'16:30', event_location:'Quinta', thumbnail_choice:'none', defer_briefing:false} };
  assert.deepEqual(parseCartInput(valid), valid);
  for (const extra of [{ customer_id:'private' }, { price_minor:1 }, { cart_id:'foreign' }]) assert.equal(parseCartInput({ ...valid, ...extra }), null);
  for (const configuration of [
    { ...valid.configuration, arbitrary:'value' },
    { ...valid.configuration, colors: 'x'.repeat(161) },
    { ...valid.configuration, event_time: '25:00' },
    { ...valid.configuration, invite_type: 'complex' },
    { ...valid.configuration, event_date: '2027-02-30' },
  ]) assert.equal(parseCartInput({ ...valid, configuration }), null);
});

test('cart result and public errors validate exact envelopes', () => {
  const item = { item_reference:'d5a9e323-6a41-42bd-a181-b89189eff10b', product_id:'14df04c5-9a9c-4eec-9a9f-b532be345ef9', product_slug:'convite', model_id:'25640e9a-9d69-41eb-aa07-d4b2d3c37e43', model_key:'standard', quantity:1, configuration:{ configuration_type:'digital_personalized', invite_type:'standard', theme:'Jardim', colors:'Azul', event_name:'Lia', event_date:'2026-11-27', event_time:'16:30', event_location:'Quinta', thumbnail_choice:'none', defer_briefing:false }, quote_snapshot_id:'a'.repeat(64), quote:{ total_minor:1234, currency:'EUR', pricing_rule_version:2 }, created_at:'2026-10-27T12:00:00Z' };
  assert.equal(isCartItem(item), true);
  assert.equal(isCartItem({ ...item, private_note:'secret' }), false);
  assert.equal(isCartError({ error:{ code:'stale_quote', message:'localized', correlation_id:'d5a9e323-6a41-42bd-a181-b89189eff10b' } }), true);
  assert.equal(isCartError({ error:{ code:'sql_error', message:'database', correlation_id:'d5a9e323-6a41-42bd-a181-b89189eff10b' } }), false);
});
