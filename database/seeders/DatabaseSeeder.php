<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import Model User
use Illuminate\Support\Facades\Hash; // Import Hashing untuk password

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Akun Admin Filament
        User::create([
            'name' => 'Yonathan Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // Password: password
            'phone_number' => '081234567890',
            'email_verified_at' => now(),
        ]);

        $this->command->info('User Admin berhasil dibuat! Email: admin@admin.com, Pass: password');
    }
}