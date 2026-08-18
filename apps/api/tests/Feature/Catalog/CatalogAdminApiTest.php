<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Application\FileReferenceStatus;
use App\Modules\Catalog\Application\FileReferenceValidator;
use App\Modules\Catalog\Application\Security\AdminIdentity;
use App\Modules\Catalog\Application\Security\AdminIdentityResolver;
use App\Modules\Catalog\Interfaces\Http\Resources\PublicCatalogProductResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CatalogAdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_writes_fail_closed_without_admin_identity(): void
    {
        $id = (string) Str::uuid();
        foreach ([
            ['POST', '/api/v1/admin/catalog/products', $this->validPayload()],
            ['PATCH', "/api/v1/admin/catalog/products/$id", ['version' => 1, 'name' => 'Bloqueado']],
            ['POST', "/api/v1/admin/catalog/products/$id/publish", ['version' => 1]],
            ['POST', "/api/v1/admin/catalog/products/$id/unpublish", ['version' => 1]],
        ] as [$method, $uri, $payload]) {
            $this->json($method, $uri, $payload)
                ->assertUnauthorized()
                ->assertExactJson(['error' => ['code' => 'unauthenticated', 'message_key' => 'auth.unauthenticated']]);
        }

        self::assertSame(0, DB::table('catalog_products')->count());
    }

    public function test_authenticated_identity_without_catalog_permission_is_forbidden(): void
    {
        $this->app->instance(AdminIdentityResolver::class, new FakeAdminIdentityResolver(new AdminIdentity('admin-1', [])));

        $this->postJson('/api/v1/admin/catalog/products', $this->validPayload())
            ->assertForbidden()
            ->assertJsonPath('error.code', 'catalog_forbidden');
    }

    public function test_authorized_admin_can_create_update_publish_and_unpublish_product(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new FakeAcceptedFileReferenceValidator);
        $categoryId = $this->category();

        $created = $this->postJson('/api/v1/admin/catalog/products', $this->validPayload($categoryId))
            ->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.version', 1)
            ->assertJsonMissingPath('data.internal_id')
            ->json('data');

        $this->patchJson('/api/v1/admin/catalog/products/'.$created['id'], [
            'version' => 1,
            'name' => 'Convite Fazendinha Premium',
        ])->assertOk()
            ->assertJsonPath('data.version', 2)
            ->assertJsonPath('data.slug', 'convite-fazendinha');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 2])
            ->assertOk()
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.version', 3);

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/unpublish', ['version' => 3])
            ->assertOk()
            ->assertJsonPath('data.status', 'unpublished')
            ->assertJsonPath('data.id', $created['id']);
    }

    public function test_invalid_payload_and_stale_version_have_stable_error_contracts(): void
    {
        $this->authorize();

        $this->postJson('/api/v1/admin/catalog/products', [
            'slug' => 'inválido',
            'modality' => 'service',
        ])->assertStatus(422)
            ->assertJsonStructure(['errors' => [['code', 'message_key', 'field_path', 'recoverable']]]);

        $payload = $this->validPayload($this->category());
        $payload['price_minor'] = '1299';
        $this->postJson('/api/v1/admin/catalog/products', $payload)
            ->assertStatus(422)
            ->assertJsonFragment(['field_path' => 'price_minor']);

        $categoryId = $this->category();
        $created = $this->postJson('/api/v1/admin/catalog/products', $this->validPayload($categoryId))->json('data');

        $this->patchJson('/api/v1/admin/catalog/products/'.$created['id'], [
            'version' => 99,
            'name' => 'Concorrente',
        ])->assertConflict()
            ->assertJsonPath('error.code', 'version_conflict');
    }

    public function test_authorized_write_to_unknown_product_returns_not_found_not_conflict(): void
    {
        $this->authorize();

        $this->patchJson('/api/v1/admin/catalog/products/'.Str::uuid(), [
            'version' => 1,
            'name' => 'Inexistente',
        ])->assertNotFound()
            ->assertJsonPath('error.code', 'product_not_found');
    }

    public function test_malformed_product_id_returns_stable_not_found_contract(): void
    {
        $this->authorize();

        $this->patchJson('/api/v1/admin/catalog/products/not-a-uuid', [
            'version' => 1,
            'name' => 'Invalido',
        ])->assertNotFound()
            ->assertJsonPath('error.code', 'not_found');
    }

    public function test_verified_rights_require_evidence_before_database_constraints(): void
    {
        $this->authorize();
        $payload = $this->validPayload($this->category());
        $payload['protected_assets'] = [[
            'label' => 'Personagem protegido',
            'status' => 'verified',
            'notes' => 'Validacao incompleta',
            'evidence_reference' => null,
        ]];

        $this->postJson('/api/v1/admin/catalog/products', $payload)
            ->assertStatus(422)
            ->assertJsonPath('errors.0.field_path', 'protected_assets.0.evidence_reference');
    }

    public function test_multiple_primary_images_are_rejected_as_field_validation(): void
    {
        $this->authorize();
        $payload = $this->validPayload($this->category());
        $payload['images'][] = [
            'storage_reference' => 'file_01HSECONDPRIMARY0000000000',
            'alt_text' => 'Outra principal',
            'sort_order' => 1,
            'is_primary' => true,
        ];

        $this->postJson('/api/v1/admin/catalog/products', $payload)
            ->assertStatus(422)
            ->assertJsonPath('errors.0.field_path', 'images');
    }

    public function test_image_contract_rejects_urls_paths_base64_and_unapproved_opaque_references(): void
    {
        $this->authorize();
        $categoryId = $this->category();

        foreach (['https://internal/files/a.png', '../private/a.png', 'data:image/png;base64,AAAA'] as $unsafe) {
            $payload = $this->validPayload($categoryId);
            $payload['images'][0]['storage_reference'] = $unsafe;
            $this->postJson('/api/v1/admin/catalog/products', $payload)
                ->assertStatus(422)
                ->assertJsonPath('errors.0.field_path', 'images.0.storage_reference');
        }

        $created = $this->postJson('/api/v1/admin/catalog/products', $this->validPayload($categoryId))
            ->assertCreated()
            ->json('data');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 1])
            ->assertStatus(422)
            ->assertJsonPath('errors.0.code', 'storage_reference_not_validated');

        self::assertSame('draft', DB::table('catalog_products')->where('id', $created['id'])->value('status'));
        self::assertSame(1, DB::table('catalog_products')->where('id', $created['id'])->value('version'));
    }

    public function test_publication_validates_secondary_image_references(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new RejectSecondFileReferenceValidator);
        $payload = $this->validPayload($this->category());
        $payload['images'][] = [
            'storage_reference' => 'file_01HSECONDARYREJECTED000000',
            'alt_text' => 'Galeria',
            'sort_order' => 1,
            'is_primary' => false,
        ];
        $created = $this->postJson('/api/v1/admin/catalog/products', $payload)->assertCreated()->json('data');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 1])
            ->assertStatus(422)
            ->assertJsonFragment(['field_path' => 'images.1.storage_reference']);
    }

    public function test_stale_publish_returns_conflict_before_publication_validation(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new FakeAcceptedFileReferenceValidator);
        $payload = $this->validPayload($this->category());
        $payload['description'] = null;
        $created = $this->postJson('/api/v1/admin/catalog/products', $payload)->assertCreated()->json('data');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 99])
            ->assertConflict()
            ->assertJsonPath('error.code', 'version_conflict');
    }

    public function test_invalid_publication_transitions_return_conflict(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new FakeAcceptedFileReferenceValidator);
        $created = $this->postJson('/api/v1/admin/catalog/products', $this->validPayload($this->category()))
            ->assertCreated()
            ->json('data');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/unpublish', ['version' => 1])
            ->assertConflict()
            ->assertJsonPath('error.code', 'not_published');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 1])->assertOk();

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 2])
            ->assertConflict()
            ->assertJsonPath('error.code', 'already_published');
    }

    public function test_publication_failure_for_incomplete_draft_or_license_is_atomic(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new FakeAcceptedFileReferenceValidator);
        $categoryId = $this->category();
        $payload = $this->validPayload($categoryId);
        $payload['description'] = null;
        $payload['protected_assets'] = [[
            'label' => 'Personagem protegido',
            'status' => 'pending',
            'notes' => 'Aguardando autorização',
            'evidence_reference' => null,
        ]];
        $created = $this->postJson('/api/v1/admin/catalog/products', $payload)->assertCreated()->json('data');

        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 1])
            ->assertStatus(422)
            ->assertJsonFragment(['field_path' => 'description'])
            ->assertJsonFragment(['code' => 'commercial_rights_not_verified']);

        $persisted = DB::table('catalog_products')->where('id', $created['id'])->first();
        self::assertSame('draft', $persisted->status);
        self::assertSame(1, (int) $persisted->version);
    }

    public function test_duplicate_slug_returns_conflict_without_leaking_database_details(): void
    {
        $this->authorize();
        $categoryId = $this->category();
        $payload = $this->validPayload($categoryId);
        $this->postJson('/api/v1/admin/catalog/products', $payload)->assertCreated();

        $this->postJson('/api/v1/admin/catalog/products', $payload)
            ->assertConflict()
            ->assertExactJson([
                'error' => [
                    'code' => 'unique_conflict',
                    'message_key' => 'catalog.conflict.unique_conflict',
                ],
            ]);
    }

    public function test_verified_rights_are_recorded_admin_only_and_public_projection_is_sanitized(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new FakeAcceptedFileReferenceValidator);
        $payload = $this->validPayload($this->category());
        $payload['protected_assets'] = [[
            'label' => 'Personagem protegido',
            'status' => 'verified',
            'notes' => 'Licença comercial validada',
            'evidence_reference' => 'evidence_01HXYZABCDEF0123456789',
        ]];

        $created = $this->postJson('/api/v1/admin/catalog/products', $payload)
            ->assertCreated()
            ->assertJsonPath('data.protected_assets.0.verified_by', 'admin-1')
            ->json('data');

        $published = $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 1])
            ->assertOk()
            ->assertJsonPath('data.status', 'published')
            ->json('data');

        $public = (new PublicCatalogProductResource($published))->resolve(request());
        self::assertArrayNotHasKey('protected_assets', $public);
        self::assertStringNotContainsString('evidence_', json_encode($public, JSON_THROW_ON_ERROR));
        self::assertStringNotContainsString('Licen', json_encode($public, JSON_THROW_ON_ERROR));
    }

    public function test_published_product_cannot_be_edited_into_an_invalid_public_state(): void
    {
        $this->authorize();
        $this->app->instance(FileReferenceValidator::class, new FakeAcceptedFileReferenceValidator);
        $created = $this->postJson('/api/v1/admin/catalog/products', $this->validPayload($this->category()))->json('data');
        $this->postJson('/api/v1/admin/catalog/products/'.$created['id'].'/publish', ['version' => 1])->assertOk();

        $this->patchJson('/api/v1/admin/catalog/products/'.$created['id'], [
            'version' => 2,
            'description' => null,
        ])->assertStatus(422)
            ->assertJsonFragment(['field_path' => 'description']);

        $persisted = DB::table('catalog_products')->where('id', $created['id'])->first();
        self::assertSame('published', $persisted->status);
        self::assertSame(2, (int) $persisted->version);
    }

    public function test_verified_evidence_cannot_be_silently_downgraded_or_rewritten(): void
    {
        $this->authorize();
        $payload = $this->validPayload($this->category());
        $payload['protected_assets'] = [[
            'label' => 'Personagem protegido',
            'status' => 'verified',
            'notes' => 'Licença validada',
            'evidence_reference' => 'evidence_01HXYZABCDEF0123456789',
        ]];
        $created = $this->postJson('/api/v1/admin/catalog/products', $payload)->assertCreated()->json('data');

        $this->patchJson('/api/v1/admin/catalog/products/'.$created['id'], [
            'version' => 1,
            'protected_assets' => [[
                'label' => 'Personagem protegido',
                'status' => 'rejected',
                'notes' => 'Rebaixado',
                'evidence_reference' => 'evidence_01HXYZABCDEF0123456789',
            ]],
        ])->assertStatus(422)
            ->assertJsonFragment(['code' => 'verified_evidence_is_immutable']);

        self::assertSame(
            'verified',
            DB::table('catalog_product_taxonomy')
                ->where('product_id', $created['id'])
                ->where('is_protected', true)
                ->value('verification_status'),
        );
    }

    private function authorize(): void
    {
        $this->app->instance(
            AdminIdentityResolver::class,
            new FakeAdminIdentityResolver(new AdminIdentity('admin-1', ['catalog.manage'])),
        );
    }

    private function category(): string
    {
        $existingId = DB::table('catalog_categories')->where('slug', 'convites')->value('id');
        if (is_string($existingId)) {
            return $existingId;
        }

        $id = (string) Str::uuid();
        DB::table('catalog_categories')->insert([
            'id' => $id,
            'slug' => 'convites',
            'label' => 'Convites',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    /** @return array<string, mixed> */
    private function validPayload(?string $categoryId = null): array
    {
        return [
            'slug' => 'convite-fazendinha',
            'name' => 'Convite Fazendinha',
            'description' => 'Convite digital pronto.',
            'modality' => 'digital_ready',
            'category_id' => $categoryId ?? (string) Str::uuid(),
            'price_minor' => 1299,
            'currency' => 'EUR',
            'availability' => 'available',
            'delivery_type' => 'digital',
            'is_personalized' => false,
            'is_immediate_delivery' => true,
            'requires_briefing' => false,
            'requires_approval' => false,
            'file_description' => 'PDF e PNG',
            'compatibility' => 'Leitores PDF',
            'usage_terms' => 'Uso pessoal',
            'taxonomy' => [['type' => 'theme', 'label' => 'Fazendinha']],
            'images' => [[
                'storage_reference' => 'file_01HXYZABCDEF0123456789ABCD',
                'alt_text' => 'Convite com animais',
                'sort_order' => 0,
                'is_primary' => true,
            ]],
            'protected_assets' => [],
        ];
    }
}

final readonly class FakeAdminIdentityResolver implements AdminIdentityResolver
{
    public function __construct(private ?AdminIdentity $identity) {}

    public function resolve(): ?AdminIdentity
    {
        return $this->identity;
    }
}

final class FakeAcceptedFileReferenceValidator implements FileReferenceValidator
{
    public function status(string $reference): FileReferenceStatus
    {
        return str_starts_with($reference, 'file_')
            ? FileReferenceStatus::Accepted
            : FileReferenceStatus::Rejected;
    }
}

final class RejectSecondFileReferenceValidator implements FileReferenceValidator
{
    public function status(string $reference): FileReferenceStatus
    {
        return $reference === 'file_01HSECONDARYREJECTED000000'
            ? FileReferenceStatus::Rejected
            : FileReferenceStatus::Accepted;
    }
}
