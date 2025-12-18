<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar user yang akan dibuat
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@abl.test',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Budi Dosen',
                'email' => 'dosen@abl.test',
                'password' => 'password',
                'role' => 'dosen',
            ],
            [
                'name' => 'Bayu Mahasiswa',
                'email' => 'mahasiswa@abl.test',
                'password' => 'password',
                'role' => 'mahasiswa',
            ],
        ];

        foreach ($users as $userData) {
            // Membuat user atau mengambil jika sudah ada (berdasarkan email)
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            // Assign role ke user tersebut
            // Pastikan RoleSeeder sudah dijalankan sebelum seeder ini
            $user->assignRole($userData['role']);
        }
    }
}
