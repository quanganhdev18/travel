<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Khởi tạo các permissions cơ bản (nếu cần)
        $permissions = [
            'manage system',
            'manage tours',
            'manage bookings',
            'manage invoices',
            'view guide schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo roles và gán permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(['manage tours', 'manage bookings', 'manage invoices']);

        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->givePermissionTo(['manage bookings', 'manage invoices']);

        $guide = Role::firstOrCreate(['name' => 'Guide']);
        $guide->givePermissionTo(['view guide schedules']);

        $cskh = Role::firstOrCreate(['name' => 'cskh', 'guard_name' => 'web']);
        
        $cskhUser = User::firstOrCreate(
            ['email' => 'cskh@travel.com'],
            [
                'name' => 'Nhân viên CSKH',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'phone' => '0987654321',
                'role' => 'cskh',
            ]
        );
        $cskhUser->assignRole($cskh);

        // Phân quyền cho một số user có sẵn dựa vào role cũ (chuỗi)
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role == 'admin') {
                $user->assignRole('Super Admin');
            } elseif ($user->role == 'staff') {
                $user->assignRole('Staff');
            } elseif ($user->role == 'guide') {
                $user->assignRole('Guide');
            }
        }
    }
}
