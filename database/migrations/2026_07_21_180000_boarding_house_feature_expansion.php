<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bh_properties', function (Blueprint $table) {
            $table->string('virtual_tour_video_url')->nullable()->after('distance_to_campus_km');
            $table->string('virtual_tour_360_url')->nullable()->after('virtual_tour_video_url');
        });

        Schema::table('bh_booking_requests', function (Blueprint $table) {
            $table->string('lease_pdf_path')->nullable()->after('responded_by');
            $table->enum('payment_status', ['none', 'pending', 'paid', 'failed'])->default('none')->after('lease_pdf_path');
        });

        Schema::create('bh_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('bh_properties')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'property_id']);
        });

        Schema::create('bh_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_request_id')->unique()->constrained('bh_booking_requests')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('bh_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('bh_conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bh_roommate_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('bio');
            $table->unsignedInteger('budget')->nullable();
            $table->enum('preferred_type', ['single', 'double', 'shared', 'studio', 'any'])->default('any');
            $table->string('preferred_city')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bh_room_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('bh_rooms')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['booked', 'blocked'])->default('blocked');
            $table->foreignId('booking_request_id')->nullable()->constrained('bh_booking_requests')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('bh_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_request_id')->constrained('bh_booking_requests')->cascadeOnDelete();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bh_payments');
        Schema::dropIfExists('bh_room_availability');
        Schema::dropIfExists('bh_roommate_posts');
        Schema::dropIfExists('bh_messages');
        Schema::dropIfExists('bh_conversations');
        Schema::dropIfExists('bh_favorites');

        Schema::table('bh_booking_requests', function (Blueprint $table) {
            $table->dropColumn(['lease_pdf_path', 'payment_status']);
        });

        Schema::table('bh_properties', function (Blueprint $table) {
            $table->dropColumn(['virtual_tour_video_url', 'virtual_tour_360_url']);
        });
    }
};
