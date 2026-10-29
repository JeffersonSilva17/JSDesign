# Security Review — Story 3.3 Implementation

## Metadata

- Artifact: Story 3.3 implementation and related contracts
- Scope: Cart session, CSRF, request/response validation, persistence, idempotency, privacy, retention, errors, and security gates
- Date: 2026-10-27
- Auditor: Vex — Security Auditor
- Gate result: Approved with one low governance observation; no open application security finding

## Evidence Collected

- Files reviewed: Cart Laravel application, domain, middleware, controllers, migration, scheduler and tests; Next.js cart routes, transport, validation, configurator and cart page; pricing quote snapshot; catalog public/admin fields; cart and pricing OpenAPI contracts; repository AGENTS.md.
- Commands: `node scripts/scan-sast.mjs`; `node scripts/scan-secrets.mjs`; `npm audit --audit-level=moderate`; `composer audit --locked --no-interaction`; `vendor/bin/phpunit`; `npm run test:bff`; `npm run typecheck`; `npm run lint`; `npm run build`; `npm run test:e2e -- --workers=1`.
- Results: Semgrep 0 findings across 324 files; Gitleaks no leaks; npm audit 0 vulnerabilities; Composer no advisories; PHPUnit 171 tests / 857 assertions / 1 skipped; BFF tests 25 passed; full Playwright suite 74 passed, followed by focused pricing suite 5/5 including the complex invitation case; typecheck, lint, and production build passed.
- References: repository code and contracts only; no external source was required.

## STRIDE Threat Model

| Category | Surface | Risk | Existing mitigation | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | BFF session and Laravel Cart routes | Forged guest identity or service caller | `__Host-` opaque session cookie; BFF service token; Laravel derives owner hash; wrong-token and owner-isolation feature tests | None confirmed |
| Tampering | Cart input and quote | Altered fields, model, amount, or replay | Closed BFF/Laravel schemas; current quote recomputed under transaction and compared; idempotency bound to owner and payload hash | None confirmed |
| Repudiation | Add and replay requests | Difficulty tracing errors without recording PII | Correlation UUID on public errors; server timestamps; no payload logging in flow | No durable actor audit required for this guest story |
| Information Disclosure | Cart read, errors, logs, URLs | Event details leaked across sessions or into diagnostics | Session-isolated reads; no-store responses; sanitized allowlisted errors; escaped JSX; 30-day purge | None confirmed |
| Denial of Service | Cart POST and upstream reads | Oversized input or request flood | 16 KiB body cap, field limits, 10/min/session rate limit, 3 s upstream timeout | GET is read-only and has no additional per-session throttle |
| Elevation of Privilege | BFF-to-Laravel boundary | Client bypasses BFF or reads another cart | Laravel requires service bearer token and validates session header; owner hash is never client-supplied in body or URL | None confirmed |

## Findings

### High Risk

Nenhum achado confirmado.

### Medium Risk

#### SEC-3.3-IMPL-01 — BFF accepted arbitrary nested configuration fields

- Affected component: `apps/web/src/bff/cartValidation.ts`
- Risk: The BFF validated the outer payload but accepted unknown fields inside `configuration`; this conflicted with the story's strict-schema boundary and allowed unneeded values to cross to Laravel.
- Project impact: Extra client data could be forwarded and persisted if server-side validation changed later.
- Recommended solution: Validate modality-specific exact keys, field types, canonical date/time, enums, and limits at the BFF.
- Evidence: Initial review found `parseCartInput` only required `isRecord(configuration)`. The implementation now calls `isCartConfiguration` for both requests and returned items; negative unit cases reject extra and invalid nested fields.
- Status: Corrigido.

#### SEC-3.3-IMPL-06 — Same-owner first submissions could race before idempotency storage

- Affected component: `AddConfiguredCartItem.php` and PostgreSQL transactions.
- Risk: Two simultaneous requests from one guest could both observe no idempotency row before either transaction committed. The unique key prevented duplicate data, but the second request could fail instead of replaying the first result.
- Project impact: A customer could receive a transient error after a successful add and see an uncertain result.
- Recommended solution: Serialize cart mutations by guest owner before reading or creating the idempotency row.
- Evidence: The application now acquires `pg_advisory_xact_lock(hashtextextended(?, 0))` with a bound owner hash before the idempotency lookup; PostgreSQL feature tests pass for add, replay, and conflicting payload paths.
- Status: Corrigido.

### Low Risk

#### SEC-3.3-IMPL-02 — Idempotency key was minted in browser code

- Affected component: `PricingConfigurator.tsx`, `/api/cart/session`
- Risk: A browser-generated nonce did not meet the story contract that the BFF issue the key.
- Project impact: The client could choose its own retry identifier, weakening the boundary's ownership of mutation metadata.
- Recommended solution: Issue a random key from the same-origin BFF session response and let Laravel bind it to the derived owner and payload hash.
- Evidence: The session route now generates a 32-byte base64url key; retry E2E verifies the same key is reused after a recoverable failure.
- Status: Corrigido.

#### SEC-3.3-IMPL-03 — API accepted MIME types by prefix

- Affected component: `AddConfiguredCartItemController.php`
- Risk: `application/jsonx` could pass a prefix check.
- Project impact: A non-JSON media type could enter JSON parsing, contrary to the strict contract.
- Recommended solution: Compare the normalized media type exactly, ignoring only parameters.
- Evidence: Controller now checks the exact normalized media type; feature test rejects `application/jsonx`.
- Status: Corrigido.

#### SEC-3.3-IMPL-04 — Upstream cart read failure looked like an empty cart

- Affected component: `cartTransport.ts`, `/api/cart/items`, cart page
- Risk: Timeout or malformed upstream data was converted to `items: []`.
- Project impact: A customer could mistake an unavailable read for deleted/lost cart data.
- Recommended solution: Preserve a distinct unavailable state and show a localized error without claiming the cart is empty.
- Evidence: `readCart` now returns `null` for upstream/schema failures; BFF returns sanitized 503, and cart SSR renders localized unavailable copy.
- Status: Corrigido.

#### SEC-3.3-IMPL-05 — Repository governance cannot prove client sandbox enforcement

- Affected component: Agent/client execution environment outside the repository
- Risk: `AGENTS.md` explicitly says its policy does not prove client sandbox, approval, or URL allowlist settings.
- Project impact: Repository controls may be stronger than the actual workstation controls.
- Recommended solution: Environment administrator should verify equivalent controls in the client/IDE; this implementation does not change those settings.
- Evidence: Repository governance text names this limitation; no client policy artifact is present in the workspace.
- Status: Aberto — low governance residual, owner: environment administrator.

## Gate Decision

- Decision: Approved with the low governance observation above; no high or medium application finding remains open.
- Conditions: Keep service token server-only, retain `jsdesign_test` isolation for migration-based tests, and verify client-side controls with the environment administrator.
- Owner: Environment administrator for the external control observation; application team for code controls.
- Next checkpoint: Review again before Story 3.3 is marked complete if session/CSRF or persistence boundaries change.
