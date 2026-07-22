<?php

namespace Database\Seeders;

use App\Models\PlatformModule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UbspSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (config('ubsp.roles') as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        foreach (config('ubsp.modules') as $slug => $module) {
            Permission::firstOrCreate(['name' => $module['permission']]);
        }

        Permission::firstOrCreate(['name' => 'platform.manage-modules']);
        Permission::firstOrCreate(['name' => 'boarding-house.manage-properties']);
        Permission::firstOrCreate(['name' => 'boarding-house.manage-bookings']);
        Permission::firstOrCreate(['name' => 'clinic.manage-patients']);
        Permission::firstOrCreate(['name' => 'clinic.manage-appointments']);

        $superAdmin = Role::findByName('super-admin');
        $superAdmin->givePermissionTo(Permission::all());

        Role::findByName('administrator')->givePermissionTo([
            'boarding-house.access',
            'boarding-house.manage-properties',
            'boarding-house.manage-bookings',
            'clinic.access',
            'clinic.manage-patients',
            'clinic.manage-appointments',
        ]);

        Role::findByName('landlord')->givePermissionTo([
            'boarding-house.access',
            'boarding-house.manage-properties',
            'boarding-house.manage-bookings',
        ]);

        Role::findByName('tenant')->givePermissionTo(['boarding-house.access']);

        Role::findByName('manager')->givePermissionTo([
            'boarding-house.access',
            'boarding-house.manage-properties',
            'boarding-house.manage-bookings',
            'clinic.access',
            'clinic.manage-patients',
            'clinic.manage-appointments',
        ]);

        Role::findByName('student')->givePermissionTo(['boarding-house.access']);

        Role::findByName('doctor')->givePermissionTo([
            'clinic.access',
            'clinic.manage-patients',
            'clinic.manage-appointments',
        ]);

        Role::findByName('customer')->givePermissionTo(['clinic.access']);

        foreach (config('ubsp.modules') as $slug => $module) {
            PlatformModule::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $module['name'],
                    'description' => $module['description'],
                    'icon' => $module['icon'],
                    'color' => $module['color'],
                    'is_enabled' => true,
                    'sort_order' => array_search($slug, array_keys(config('ubsp.modules'))) + 1,
                ]
            );
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@ubsp.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');

        $demo = User::firstOrCreate(
            ['email' => 'demo@ubsp.local'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $demo->assignRole('staff');
        $demo->givePermissionTo(collect(config('ubsp.modules'))->pluck('permission')->toArray());

        $landlord = User::firstOrCreate(
            ['email' => 'landlord@ubsp.local'],
            [
                'name' => 'Jane Landlord',
                'password' => Hash::make('password'),
                'phone' => '+263771234567',
                'email_verified_at' => now(),
            ]
        );
        $landlord->assignRole('landlord');

        $student = User::firstOrCreate(
            ['email' => 'student@ubsp.local'],
            [
                'name' => 'Alex Student',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $student->assignRole('student');

        $doctor = User::firstOrCreate(
            ['email' => 'doctor@ubsp.local'],
            [
                'name' => 'Dr. Sarah Moyo',
                'password' => Hash::make('password'),
                'phone' => '+263771111222',
                'email_verified_at' => now(),
            ]
        );
        $doctor->assignRole('doctor');

        $patient = User::firstOrCreate(
            ['email' => 'patient@ubsp.local'],
            [
                'name' => 'John Patient',
                'password' => Hash::make('password'),
                'phone' => '+263773333444',
                'email_verified_at' => now(),
            ]
        );
        $patient->assignRole('customer');
    }
}
