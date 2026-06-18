<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'cskh', 'guard_name' => 'web']);
        
        $cskhUser = \App\Models\User::firstOrCreate(
            ['email' => 'cskh@travel.com'],
            [
                'name' => 'Nhân viên CSKH',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'phone' => '0987654321',
                'role' => 'cskh',
            ]
        );
        $cskhUser->assignRole($role);
    }
}
