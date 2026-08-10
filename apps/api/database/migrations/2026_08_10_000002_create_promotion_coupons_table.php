<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_coupons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('promotion_campaigns')->cascadeOnDelete();
            $table->string('purpose', 80);
            $table->uuid('public_request_id')->unique();
            $table->string('email_canonical', 254)->nullable();
            $table->string('email_for_delivery', 254)->nullable();
            $table->string('email_fingerprint', 128)->nullable();
            $table->string('email_fingerprint_key_version', 40)->nullable();
            $table->string('authorization_text_version', 80);
            $table->timestampTz('authorization_accepted_at');
            $table->string('legal_basis', 80)->nullable();
            $table->string('retention_policy_version', 80)->nullable();
            $table->timestampTz('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->timestampTz('suppressed_at')->nullable();
            $table->string('suppressed_reason')->nullable();
            $table->timestampTz('anonymized_at')->nullable();
            $table->string('anonymized_reason')->nullable();
            $table->string('code_digest', 128)->nullable()->unique();
            $table->text('code_encrypted')->nullable();
            $table->string('code_key_version', 40)->nullable();
            $table->unsignedSmallInteger('discount_percent');
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->boolean('non_cumulative')->default(true);
            $table->boolean('manual_checkout_required')->default(true);
            $table->timestampTz('expires_at')->nullable();
            $table->string('delivery_mode', 40);
            $table->string('issuance_state', 40);
            $table->string('delivery_state', 40);
            $table->string('user_agent_hash', 128)->nullable();
            $table->unsignedInteger('request_count')->default(1);
            $table->timestampTz('last_requested_at')->nullable();
            $table->timestampsTz();
        });

        DB::statement("CREATE UNIQUE INDEX promotion_coupons_one_active_first_purchase_email ON promotion_coupons (purpose, email_fingerprint_key_version, email_fingerprint) WHERE purpose = 'first_purchase' AND revoked_at IS NULL AND suppressed_at IS NULL AND anonymized_at IS NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_coupons');
    }
};
