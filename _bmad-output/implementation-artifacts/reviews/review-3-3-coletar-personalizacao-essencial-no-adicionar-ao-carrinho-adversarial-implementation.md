# Adversarial Review — Story 3.3 Implementation

- Artifact reviewed: uncommitted Story 3.3 implementation, contracts, and tests
- Date: 2026-10-27
- Result: 11 findings recorded; all application findings corrected or covered with regression evidence

## Findings

### ADV-3.3-01 — Nested request configuration was not closed

- Severity: Medium
- Evidence: `parseCartInput` originally checked only that `configuration` was an object.
- Recommendation: Apply a modality-specific exact schema at the BFF boundary.
- Treatment: `isCartConfiguration` now rejects extra keys, incorrect types, unsupported enums, bad dates/times, and oversized values; unit tests cover these cases.
- Status: Corrigido.

### ADV-3.3-02 — Nested cart responses were opaque to BFF validation

- Severity: Medium
- Evidence: The outer result envelope was strict while `configuration` accepted any object.
- Recommendation: Validate returned configuration using the same closed, model-aware schema.
- Treatment: `isCartItem` now checks the nested configuration and unit tests reject unexpected response fields.
- Status: Corrigido.

### ADV-3.3-03 — Browser created the idempotency key

- Severity: Medium
- Evidence: `PricingConfigurator` initially used `crypto.randomUUID()` for the key.
- Recommendation: Issue the nonce at the BFF and bind it server-side to the guest session and payload hash.
- Treatment: `/api/cart/session` now issues a random key; Laravel records it with owner hash and canonical payload hash; E2E verifies retry reuse.
- Status: Corrigido.

### ADV-3.3-04 — API media type used a prefix check

- Severity: Low
- Evidence: The controller accepted any `Content-Type` beginning with `application/json`.
- Recommendation: Require exact normalized `application/json` before parsing.
- Treatment: The controller now compares the normalized media type exactly and a feature test rejects `application/jsonx`.
- Status: Corrigido.

### ADV-3.3-05 — Cart read outage was indistinguishable from an empty cart

- Severity: Medium
- Evidence: `readCart` previously returned `[]` for timeouts, malformed bodies, and upstream errors.
- Recommendation: Keep unavailable and empty states separate.
- Treatment: Transport returns `null` for failures; the BFF emits sanitized 503 and the cart page shows localized unavailable copy.
- Status: Corrigido.

### ADV-3.3-06 — Guest ownership had no direct isolation regression test

- Severity: Medium
- Evidence: Initial feature coverage proved add/replay but did not compare reads across two guest sessions.
- Recommendation: Verify same-session read succeeds and another owner sees no data.
- Treatment: `test_cart_reads_are_isolated_to_the_guest_session` verifies the item is visible only to its owner and wrong service credentials fail closed. The complex invitation prefill is also read back unchanged for the later Briefing flow.
- Status: Corrigido.

### ADV-3.3-07 — Personal-data expiration was not tested

- Severity: Medium
- Evidence: A scheduled purge existed, but no test proved expiration cascaded through item configuration and idempotency references.
- Recommendation: Test the scheduled command against expired guest data.
- Treatment: Feature test expires cart/key records, runs `cart:purge-expired`, and asserts cart, item, and key tables are empty.
- Status: Corrigido.

### ADV-3.3-08 — Retry preservation and key reuse lacked browser evidence

- Severity: Medium
- Evidence: Earlier browser coverage verified successful inclusion and quote failure, but not a failed cart POST followed by retry.
- Recommendation: Simulate recoverable failure, assert no redirect, preserved fields, and stable key on retry.
- Treatment: New Playwright scenario performs those assertions before accepting the second request.
- Status: Corrigido.

### ADV-3.3-09 — Cart read error did not use the selected locale

- Severity: Low
- Evidence: The initial GET error path used the default Portuguese message.
- Recommendation: Derive messages from the manually selected locale used by the configurator.
- Treatment: GET route selects locale from the request header and uses the existing allowlist; cart SSR uses the locale cookie.
- Status: Corrigido.

### ADV-3.3-10 — Concurrent test commands shared a destructive test database

- Severity: Low
- Evidence: A PHPUnit run overlapped with Playwright's `migrate:fresh` setup against `jsdesign_test`, causing missing-table and fixture failures.
- Recommendation: Run the two migration-based suites serially against the approved test database.
- Treatment: The affected run was discarded; PHPUnit was rerun alone and passed 171 tests / 857 assertions / 1 skipped. The full Playwright run is serialized with one worker and passed.
- Status: Tratado operacionalmente; no product-code change required.

### ADV-3.3-11 — Concurrent first submissions could race before idempotency storage

- Severity: Medium
- Evidence: Two simultaneous requests could both observe no idempotency row before the first commit; the unique constraint prevented duplicates but could turn the later request into a 503.
- Recommendation: Serialize writes for one guest owner before checking the idempotency key.
- Treatment: Cart application acquires a transaction-scoped PostgreSQL advisory lock from the owner hash before the idempotency lookup. The full PostgreSQL feature suite passes add, replay, and conflict paths.
- Status: Corrigido.

## Verification

- `vendor/bin/phpunit`: 171 tests, 857 assertions, 1 skipped.
- `npm run test:bff`: 25 passed.
- `npm run test:e2e -- --workers=1`: 74 passed; focused pricing suite after adding the complex invitation scenario: 5 passed.
- `npm run typecheck`, `npm run lint`, and `npm run build`: passed.
- Semgrep: 0 findings; Gitleaks: no leaks; `npm audit`: 0 vulnerabilities; `composer audit`: no advisories.
