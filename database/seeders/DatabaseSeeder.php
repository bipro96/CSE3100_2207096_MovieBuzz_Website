<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'bipro@moviebuzz.com'],
            [
                'name' => 'MovieBuzz Admin',
                'password' => Hash::make('bipro096'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        Wallet::firstOrCreate(['user_id' => $admin->id], ['balance' => 0]);

        $demo = User::firstOrCreate(
            ['email' => 'bbcustomer@moviebuzz.com'],
            [
                'name' => 'BBCustomer',
                'password' => Hash::make('bipro096'),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        Wallet::firstOrCreate(['user_id' => $demo->id], ['balance' => 500]);

        $genres = [
            'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary',
            'Drama', 'Family', 'Fantasy', 'Horror', 'Mystery', 'Romance',
            'Science Fiction', 'Thriller', 'War', 'Western',
        ];

        foreach ($genres as $name) {
            Genre::firstOrCreate(['name' => $name], ['slug' => Str::slug($name)]);
        }

        $this->command->info('Admin login: bipro@moviebuzz.com / bipro096');
        $this->command->info('Customer login: bbcustomer@moviebuzz.com / bipro096 (wallet preloaded with ৳500)');
    }
}
