<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Create Super Admin
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@taptap.com',
        ]);
        $admin->assignRole('super_admin');

        // Create a Restaurant
        $restaurant = \App\Models\Restaurant::create([
            'name' => 'TAPTAP Demo Grill',
            'location' => 'Dar es Salaam',
            'phone' => '0700000000',
        ]);
        $restaurant->ensureDefaultCatalogCategories();

        // Create Manager
        $manager = User::factory()->create([
            'name' => 'Manager One',
            'email' => 'manager@taptap.com',
            'restaurant_id' => $restaurant->id,
        ]);
        $manager->assignRole('manager');

        // Create Waiter
        $waiter = User::factory()->create([
            'name' => 'Waiter One',
            'email' => 'waiter@taptap.com',
            'restaurant_id' => $restaurant->id,
        ]);
        $waiter->assignRole('waiter');
    }
}
