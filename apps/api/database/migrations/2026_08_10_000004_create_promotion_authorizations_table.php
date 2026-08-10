<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_authorizations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained('promotion_coupons')->cascadeOnDelete();
            $table->string('text_version', 80);
            $table->string('legal_basis', 80);
            $table->string('retention_policy_version', 80);
            $table->timestampTz('accepted_at');
            $table->timestampTz('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->timestampsTz();

            $table->unique(['coupon_id', 'text_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_authorizations');
    }
};
