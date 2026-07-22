<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Electronics Tracker
        Schema::create('et_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['computer', 'phone', 'laptop', 'printer', 'accessory']);
            $table->string('serial_number')->unique();
            $table->string('qr_code')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'maintenance', 'disposed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('et_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('et_assets')->cascadeOnDelete();
            $table->date('service_date');
            $table->string('description');
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('et_disposal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('et_assets')->cascadeOnDelete();
            $table->date('disposal_date');
            $table->string('method');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // 2. University Social
        Schema::create('us_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('us_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('us_departments')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('us_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('us_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('us_groups')->nullOnDelete();
            $table->text('content');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('us_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('us_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['post_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('us_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('us_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('us_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('starts_at');
            $table->datetime('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('us_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('department_id')->nullable()->constrained('us_departments')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('us_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('us_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 3. Balanced Scorecard
        Schema::create('bsc_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('bsc_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('bsc_departments')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bsc_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objective_id')->constrained('bsc_objectives')->cascadeOnDelete();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->decimal('target', 12, 2);
            $table->decimal('actual', 12, 2)->default(0);
            $table->enum('status', ['green', 'yellow', 'red'])->default('green');
            $table->timestamps();
        });

        // 4. Marketplace
        Schema::create('mp_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('mp_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('mp_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->enum('status', ['active', 'sold', 'draft'])->default('active');
            $table->timestamps();
        });

        Schema::create('mp_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('mp_products')->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('mp_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('mp_products')->cascadeOnDelete();
            $table->unique(['user_id', 'product_id']);
            $table->timestamps();
        });

        Schema::create('mp_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('mp_products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('mp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('mp_products')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // 5. Boarding House
        Schema::create('bh_landlords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('bh_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('bh_landlords')->cascadeOnDelete();
            $table->string('name');
            $table->text('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bh_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('bh_properties')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('bh_booking_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('bh_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('move_in_date')->nullable();
            $table->timestamps();
        });

        Schema::create('bh_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('bh_properties')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // 6. Exchange Tracker
        Schema::create('ex_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3);
            $table->decimal('rate', 12, 6);
            $table->date('recorded_date');
            $table->timestamps();
        });

        Schema::create('ex_commodities', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['fuel', 'food', 'market']);
            $table->string('name');
            $table->string('unit')->nullable();
            $table->decimal('price', 12, 2);
            $table->date('recorded_date');
            $table->timestamps();
        });

        Schema::create('ex_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_type');
            $table->string('item_name');
            $table->decimal('threshold', 12, 2);
            $table->enum('condition', ['above', 'below']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Weather (cached data)
        Schema::create('wx_locations', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->json('forecast_cache')->nullable();
            $table->timestamp('cached_at')->nullable();
            $table->timestamps();
        });

        // 8. Clinic
        Schema::create('cl_patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('cl_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->timestamps();
        });

        Schema::create('cl_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('cl_patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('cl_doctors')->cascadeOnDelete();
            $table->datetime('scheduled_at');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cl_medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('cl_patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('cl_doctors')->cascadeOnDelete();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->timestamps();
        });

        Schema::create('cl_lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('cl_patients')->cascadeOnDelete();
            $table->string('test_name');
            $table->text('result');
            $table->date('test_date');
            $table->timestamps();
        });

        Schema::create('cl_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('cl_patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('cl_doctors')->cascadeOnDelete();
            $table->text('medication');
            $table->text('dosage')->nullable();
            $table->timestamps();
        });

        Schema::create('cl_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('cl_patients')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // 9. M&E
        Schema::create('me_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 14, 2)->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });

        Schema::create('me_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('me_projects')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });

        Schema::create('me_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('me_projects')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('target', 12, 2);
            $table->decimal('actual', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('me_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('me_projects')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 10. Subscription Sharing
        Schema::create('ss_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->decimal('monthly_cost', 10, 2);
            $table->unsignedTinyInteger('max_members');
            $table->date('renewal_date')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('ss_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('ss_plans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->unique(['plan_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('ss_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('ss_plans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->timestamps();
        });

        // 11. Sports League
        Schema::create('sl_leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('season')->nullable();
            $table->timestamps();
        });

        Schema::create('sl_venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('sl_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('sl_leagues')->cascadeOnDelete();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('sl_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('sl_teams')->cascadeOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->unsignedSmallInteger('goals')->default(0);
            $table->timestamps();
        });

        Schema::create('sl_fixtures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('sl_leagues')->cascadeOnDelete();
            $table->foreignId('home_team_id')->constrained('sl_teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('sl_teams')->cascadeOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained('sl_venues')->nullOnDelete();
            $table->datetime('scheduled_at');
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed'])->default('scheduled');
            $table->timestamps();
        });

        Schema::create('sl_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('sl_leagues')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'sl_news', 'sl_fixtures', 'sl_players', 'sl_teams', 'sl_venues', 'sl_leagues',
            'ss_usage_logs', 'ss_members', 'ss_plans',
            'me_evidence', 'me_indicators', 'me_activities', 'me_projects',
            'cl_bills', 'cl_prescriptions', 'cl_lab_results', 'cl_medical_records',
            'cl_appointments', 'cl_doctors', 'cl_patients',
            'wx_locations',
            'ex_alerts', 'ex_commodities', 'ex_rates',
            'bh_reviews', 'bh_booking_requests', 'bh_rooms', 'bh_properties', 'bh_landlords',
            'mp_messages', 'mp_reviews', 'mp_wishlists', 'mp_product_images', 'mp_products', 'mp_categories',
            'bsc_kpis', 'bsc_objectives', 'bsc_departments',
            'us_notifications', 'us_messages', 'us_announcements', 'us_events',
            'us_comments', 'us_post_likes', 'us_posts', 'us_groups', 'us_courses', 'us_departments',
            'et_disposal_logs', 'et_maintenance_logs', 'et_assets',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
