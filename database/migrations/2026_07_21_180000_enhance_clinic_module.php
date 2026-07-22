<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cl_patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained()->nullOnDelete();
            $table->string('blood_type')->nullable()->after('address');
            $table->text('allergies')->nullable()->after('blood_type');
            $table->text('emergency_contact')->nullable()->after('allergies');
        });

        Schema::table('cl_doctors', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('license_number');
            $table->boolean('accepts_appointments')->default(true)->after('bio');
        });

        Schema::table('cl_appointments', function (Blueprint $table) {
            $table->string('reason')->nullable()->after('doctor_id');
            $table->text('patient_notes')->nullable()->after('notes');
            $table->text('provider_notes')->nullable()->after('patient_notes');
            $table->timestamp('responded_at')->nullable()->after('status');
            $table->foreignId('responded_by')->nullable()->after('responded_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('cl_medical_records', function (Blueprint $table) {
            $table->date('visit_date')->nullable()->after('doctor_id');
            $table->string('record_type')->default('consultation')->after('visit_date');
        });

        Schema::table('cl_prescriptions', function (Blueprint $table) {
            $table->date('prescribed_date')->nullable()->after('doctor_id');
            $table->boolean('is_active')->default(true)->after('dosage');
        });
    }

    public function down(): void
    {
        Schema::table('cl_prescriptions', function (Blueprint $table) {
            $table->dropColumn(['prescribed_date', 'is_active']);
        });

        Schema::table('cl_medical_records', function (Blueprint $table) {
            $table->dropColumn(['visit_date', 'record_type']);
        });

        Schema::table('cl_appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responded_by');
            $table->dropColumn(['reason', 'patient_notes', 'provider_notes', 'responded_at']);
        });

        Schema::table('cl_doctors', function (Blueprint $table) {
            $table->dropColumn(['bio', 'accepts_appointments']);
        });

        Schema::table('cl_patients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['blood_type', 'allergies', 'emergency_contact']);
        });
    }
};
