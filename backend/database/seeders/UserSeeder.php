<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin demo user
        User::create([
            'name'     => 'Demo User',
            'email'    => 'demo@taskflow.dev',
            'password' => Hash::make('password'),
        ]);

        // Additional team members
        $members = [
            ['name' => 'Alice Johnson', 'email' => 'alice@taskflow.dev'],
            ['name' => 'Bob Smith', 'email' => 'bob@taskflow.dev'],
            ['name' => 'Carol White', 'email' => 'carol@taskflow.dev'],
        ];

        foreach ($members as $member) {
            User::create([...$member, 'password' => Hash::make('password')]);
        }
    }
}
