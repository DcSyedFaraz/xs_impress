<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\Supplier::create([
             'name' => 'PromiData',
             'supplier_code' => '1001',
         ]);
         
         $this->call([
            CreateAdminUserSeeder::class
        ]);
    }
}
