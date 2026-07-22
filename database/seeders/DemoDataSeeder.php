<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@ubsp.local')->value('id');

        DB::table('bsc_departments')->insertOrIgnore([
            ['id' => 1, 'name' => 'Operations', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('bsc_objectives')->insertOrIgnore([
            ['id' => 1, 'department_id' => 1, 'title' => 'Improve Efficiency', 'description' => 'Operational excellence', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('mp_categories')->insertOrIgnore([
            ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Furniture', 'slug' => 'furniture', 'created_at' => now(), 'updated_at' => now()],
        ]);

        if ($adminId) {
            DB::table('bh_landlords')->insertOrIgnore([
                ['id' => 1, 'user_id' => $adminId, 'phone' => '+1234567890', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
