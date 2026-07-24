<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_modules', function (Blueprint $table) {
            $table->decimal('price_zmw', 10, 2)->default(0)->after('sort_order');
        });

        Schema::create('kcpay_settings', function (Blueprint $table) {
            $table->id();
            $table->string('base_url')->default('https://productcheckout.kundananjicreations.com/');
            $table->string('api_username')->nullable();
            $table->text('api_password')->nullable();
            $table->string('public_key')->nullable();
            $table->text('private_key')->nullable();
            $table->string('product_reference')->nullable();
            $table->string('source_name')->default('UBSP');
            $table->string('mode')->default('test');
            $table->string('callback_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('registration_payments', function (Blueprint $table) {
            $table->id();
            $table->string('seller_reference')->unique();
            $table->decimal('amount_zmw', 10, 2);
            $table->string('currency', 3)->default('ZMW');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('network')->nullable();
            $table->string('phone')->nullable();
            $table->string('token')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('modules')->nullable();
            $table->text('registration_payload');
            $table->json('kcpay_init_response')->nullable();
            $table->json('kcpay_callback_payload')->nullable();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_payments');
        Schema::dropIfExists('kcpay_settings');

        Schema::table('platform_modules', function (Blueprint $table) {
            $table->dropColumn('price_zmw');
        });
    }
};
