<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LaravelCmsUser;
use Illuminate\Support\Facades\Hash;

class LaravelCmsUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Cek apakah user sudah ada
        $adminEmail = 'admin@omniflow.id';
        
        if (! LaravelCmsUser::where('email', $adminEmail)->exists()) {
            LaravelCmsUser::create([
                'name' => 'Super Admin',
                'email' => $adminEmail,
                'password' => Hash::make('password'),
            ]);
            $this->command->info('✅ Super Admin created (admin@omniflow.id).');
        } else {
            $this->command->info('ℹ️ Super Admin already exists.');
        }
    }
}
