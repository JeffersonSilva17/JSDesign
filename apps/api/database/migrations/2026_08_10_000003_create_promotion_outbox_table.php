<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_outbox', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained('promotion_coupons')->cascadeOnDelete();
            $table->string('notification_key', 220)->unique();
            $table->string('channel', 40);
            $table->string('purpose', 80);
            $table->string('template_version', 80);
            $table->string('state', 40);
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestampTz('available_at')->nullable();
            $table->timestampTz('sent_at')->nullable();
            $table->timestampTz('failed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_outbox');
    }
};
