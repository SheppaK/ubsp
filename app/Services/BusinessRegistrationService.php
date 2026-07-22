<?php

namespace App\Services;

use App\Mail\TenantAccountCreatedMail;
use App\Models\Business;
use App\Models\BusinessModule;
use App\Models\BusinessUser;
use App\Models\Modules\BoardingHouse\Landlord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class BusinessRegistrationService
{
    public function __construct(
        protected PhpMailerService $mailer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $moduleSlugs
     */
    public function register(array $data, array $moduleSlugs): Business
    {
        return DB::transaction(function () use ($data, $moduleSlugs) {
            $user = User::create([
                'name' => $data['owner_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('business-owner');

            $business = Business::create([
                'owner_id' => $user->id,
                'name' => $data['business_name'],
                'slug' => Business::generateSlug($data['business_name']),
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'active',
            ]);

            BusinessUser::create([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'role' => 'owner',
            ]);

            $this->activateModules($business, $moduleSlugs, $user);

            return $business->load('modules');
        });
    }

    /**
     * @param  list<string>  $moduleSlugs
     */
    public function activateModules(Business $business, array $moduleSlugs, User $user): void
    {
        $permissions = [];

        foreach ($moduleSlugs as $slug) {
            if (! config("ubsp.modules.{$slug}")) {
                continue;
            }

            BusinessModule::updateOrCreate(
                ['business_id' => $business->id, 'module_slug' => $slug],
                ['is_active' => true]
            );

            $modulePerms = config("ubsp.module_owner_permissions.{$slug}", [
                config("ubsp.modules.{$slug}.permission"),
            ]);

            $permissions = array_merge($permissions, $modulePerms);

            if ($slug === 'boarding-house') {
                $this->provisionLandlord($business, $user);
            }
        }

        $permissions = array_unique(array_filter($permissions));

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $user->givePermissionTo($permissions);
    }

    protected function provisionLandlord(Business $business, User $user): void
    {
        Landlord::updateOrCreate(
            ['user_id' => $user->id],
            [
                'business_id' => $business->id,
                'phone' => $user->phone ?? $business->phone,
                'business_name' => $business->name,
                'is_verified' => true,
            ]
        );

        if (! $user->hasRole('landlord')) {
            $user->assignRole('landlord');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createTenantAccount(Business $business, User $inviter, array $data): User
    {
        $plainPassword = Str::password(12);

        $tenant = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
            'created_by_business_id' => $business->id,
        ]);

        $tenant->assignRole('tenant');
        $tenant->givePermissionTo('boarding-house.access');

        BusinessUser::create([
            'business_id' => $business->id,
            'user_id' => $tenant->id,
            'role' => 'tenant',
            'invited_by' => $inviter->id,
        ]);

        try {
            $this->sendTenantCredentials($tenant, $plainPassword, $business);
            session()->flash('email_sent', true);
        } catch (\Throwable $e) {
            report($e);
            session()->flash('email_sent', false);
            session()->flash('temp_password', $plainPassword);
        }

        return $tenant;
    }

    protected function sendTenantCredentials(User $tenant, string $plainPassword, Business $business): void
    {
        $this->mailer->applyToLaravelConfig();

        $mailable = new TenantAccountCreatedMail($tenant, $plainPassword, $business);

        if ($this->mailer->isConfigured()) {
            Mail::to($tenant->email)->send($mailable);
        } else {
            $this->mailer->send(
                $tenant->email,
                'Your UBSP Tenant Account — '.$business->name,
                view('emails.tenant-account-created-html', [
                    'user' => $tenant,
                    'password' => $plainPassword,
                    'business' => $business,
                    'loginUrl' => route('login'),
                    'resetUrl' => route('password.request'),
                ])->render(),
                $tenant->name
            );
        }
    }
}
