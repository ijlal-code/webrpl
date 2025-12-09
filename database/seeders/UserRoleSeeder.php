<?php

namespace Database\Seeders;

use App\Models\Sopir;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $adminUsers = User::factory()
            ->count(2)
            ->admin()
            ->create(['password' => $defaultPassword]);

        $penumpangUsers = User::factory()
            ->count(10)
            ->penumpang()
            ->create(['password' => $defaultPassword]);

        $sopirUsers = User::factory()
            ->count(6)
            ->sopir()
            ->create(['password' => $defaultPassword]);

        foreach ($sopirUsers as $index => $user) {
            Sopir::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'nomor_sim' => 'SIM' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
                    'telepon' => $user->phone ?? '08' . fake()->numerify('##########'),
                    'pengalaman' => 'Terdaftar melalui seeder massal',
                ]
            );
        }

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Demo',
                'password' => $defaultPassword,
                'role' => 'admin',
                'phone' => '0811111111',
                
            ]
        );

        User::firstOrCreate(
            ['email' => 'penumpang@example.com'],
            [
                'name' => 'Penumpang Demo',
                'password' => $defaultPassword,
                'role' => 'penumpang',
                'phone' => '0822222222',
                
            ]
        );

        $demoSopirUser = User::firstOrCreate(
            ['email' => 'sopir@example.com'],
            [
                'name' => 'Sopir Demo',
                'password' => $defaultPassword,
                'role' => 'sopir',
                'phone' => '0833333333',
                
            ]
        );

        Sopir::updateOrCreate(
            ['user_id' => $demoSopirUser->id],
            [
                'nama' => $demoSopirUser->name,
                'nomor_sim' => 'SIM-99999',
                'telepon' => $demoSopirUser->phone,
                'pengalaman' => 'Siap menerima pesanan demo',
            ]
        );

        // Pastikan data minimal tetap tersedia ketika seeder dipanggil terpisah.
        if ($adminUsers->isEmpty() && $penumpangUsers->isEmpty()) {
            User::factory()->count(3)->penumpang()->create(['password' => $defaultPassword]);
        }
    }
}
