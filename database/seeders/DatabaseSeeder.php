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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Product::query()->insert([
            [
                'name' => 'Flag of Brazil',
                'image_url' => 'https://flagcdn.com/w320/br.png',
                'price' => 48.00,
                'stock_quantity' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flag of Portugal',
                'image_url' => 'https://flagcdn.com/w320/pt.png',
                'price' => 24.50,
                'stock_quantity' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flag of USA',
                'image_url' => 'https://flagcdn.com/w320/us.png',
                'price' => 89.99,
                'stock_quantity' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flag of Germany',
                'image_url' => 'https://flagcdn.com/w320/de.png',
                'price' => 35.00,
                'stock_quantity' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
