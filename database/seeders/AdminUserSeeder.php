<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@threadsembroideryprint.co.uk'],
            [
                'name' => 'Threads Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'company_name' => 'Threads Embroidery & Print',
            ]
        );
    }
}
