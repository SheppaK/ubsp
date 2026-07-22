<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bh_landlords', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('user_id');
            $table->text('bio')->nullable()->after('phone');
            $table->boolean('is_verified')->default(false)->after('bio');
        });

        Schema::table('bh_properties', function (Blueprint $table) {
            $table->string('city')->nullable()->after('address');
            $table->string('cover_image')->nullable()->after('description');
            $table->json('amenities')->nullable()->after('cover_image');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('amenities');
            $table->unsignedTinyInteger('distance_to_campus_km')->nullable()->after('status');
        });

        Schema::table('bh_rooms', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->unsignedTinyInteger('capacity')->default(1)->after('price');
            $table->enum('type', ['single', 'double', 'shared', 'studio'])->default('single')->after('capacity');
        });

        Schema::create('bh_property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('bh_properties')->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_cover')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('bh_booking_requests', function (Blueprint $table) {
            $table->text('message')->nullable()->after('move_in_date');
            $table->unsignedTinyInteger('duration_months')->default(1)->after('message');
            $table->timestamp('responded_at')->nullable()->after('status');
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete()->after('responded_at');
        });

        Schema::table('bh_reviews', function (Blueprint $table) {
            $table->unique(['property_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('bh_reviews', function (Blueprint $table) {
            $table->dropUnique(['property_id', 'user_id']);
        });

        Schema::table('bh_booking_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responded_by');
            $table->dropColumn(['message', 'duration_months', 'responded_at']);
        });

        Schema::dropIfExists('bh_property_images');

        Schema::table('bh_rooms', function (Blueprint $table) {
            $table->dropColumn(['description', 'capacity', 'type']);
        });

        Schema::table('bh_properties', function (Blueprint $table) {
            $table->dropColumn(['city', 'cover_image', 'amenities', 'status', 'distance_to_campus_km']);
        });

        Schema::table('bh_landlords', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'bio', 'is_verified']);
        });
    }
};
