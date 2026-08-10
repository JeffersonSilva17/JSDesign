<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('purpose', 80);
            $table->string('slug', 120)->unique();
            $table->string('name', 160);
            $table->string('terms_hash', 64)->unique();
            $table->boolean('is_active')->default(false);
            $table->unsignedSmallInteger('discount_percent');
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->boolean('non_cumulative')->default(true);
            $table->boolean('manual_checkout_required')->default(true);
            $table->string('authorization_text_version', 80);
            $table->string('legal_basis', 80)->nullable();
            $table->string('retention_policy_version', 80)->nullable();
            $table->string('delivery_mode', 40);
            $table->timestampTz('expires_at')->nullable();
            $table->timestampsTz();
        });

        $activeCondition = DB::connection()->getDriverName() === 'pgsql'
            ? "purpose = 'first_purchase' AND is_active = true"
            : "purpose = 'first_purchase' AND is_active = 1";

        DB::statement("CREATE UNIQUE INDEX promotion_campaigns_one_active_first_purchase ON promotion_campaigns (purpose) WHERE $activeCondition");
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_campaigns');
    }
};
