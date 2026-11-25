<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        $email = config('deploy.admin.email');
        $password = config('deploy.admin.password');
        $name = config('deploy.admin.name', 'Admin');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            return;
        }

        User::query()->create([
            'name' => $name,
            'email' => mb_strtolower(trim($email)),
            'password' => $password,
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_active' => true,
        ]);
    }
}
