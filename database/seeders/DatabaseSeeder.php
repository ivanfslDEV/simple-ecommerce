<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Product::query()->insert([
            [
                'name' => 'Canvas Backpack',
                'image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80',
                'price' => 48.00,
                'stock_quantity' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stainless Water Bottle',
                'image_url' => 'https://images.unsplash.com/photo-1526406915894-6c2283c7b2b4?auto=format&fit=crop&w=800&q=80',
                'price' => 24.50,
                'stock_quantity' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wireless Earbuds',
                'image_url' => 'https://images.unsplash.com/photo-1518441902111-c1d1f1fe9c2d?auto=format&fit=crop&w=800&q=80',
                'price' => 89.99,
                'stock_quantity' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Desk Lamp',
                'image_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=800&q=80',
                'price' => 35.00,
                'stock_quantity' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
