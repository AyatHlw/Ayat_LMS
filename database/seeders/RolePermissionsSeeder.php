<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // defining roles .
        $superAdminRole = Role::create(['name' => 'superAdmin']);
        $adminRole = Role::create(['name' => 'admin']);
        $hrRole = Role::create(['name' => 'HR']);
        $courseCreatorRole = Role::create(['name' => 'courseCreator']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);
        // editable
        $permissions = [
            'sign up', 'sign in', 'sign out',
            'approve for users', 'block users',
            'course.add', 'show pending users',
            'course.comment', 'course.rating',
            // ..
        ];
        $superAdminPermissions = [
            'sign up', 'sign in', 'sign out',
            'approve for users', 'block users'
            // ..
        ];
        $adminPermissions = [
            'sign up', 'sign in', 'sign out',
            'show pending users', 'block users',
            // ..
        ];
        $hrPermissions = [
            'sign up', 'sign in', 'sign out',
            // ..
        ];
        $courseCreatorPermissions = [
            'sign up', 'sign in', 'sign out',
            'course.add',
            // ..
        ];
        $teacherPermissions = [
            'sign up', 'sign in', 'sign out',
            'course.comment', 'course.rating',
            // ..
        ];
        $studentPermissions = [
            'sign up', 'sign in', 'sign out',
            'course.comment', 'course.rating',
            // ..
        ];
        // insert the permissions in database
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdminRole->syncPermissions($superAdminPermissions);
        $adminRole->syncPermissions($adminPermissions);
        $hrRole->syncPermissions($hrPermissions);
        $courseCreatorRole->syncPermissions($courseCreatorPermissions);
        $teacherRole->syncPermissions($teacherPermissions);
        $studentRole->syncPermissions($studentPermissions);

        $superAdmin = User::factory()->create([
            'name' => 'Super_Admin',
            'email' => 'superAdmin@gmail.com',
            'password' => Hash::make('123456789')
        ]);

        $superAdmin->assignRole($superAdminRole);
        $permissions = $superAdminRole->permissions()->pluck('name')->toArray();
        $superAdmin->givePermissionTo($permissions);

        $admin1 = User::factory()->create([
            'name' => 'Admin1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $admin1->assignRole($adminRole);
        $permissions = $adminRole->permissions()->pluck('name')->toArray();
        $admin1->givePermissionTo($permissions);

        $admin2 = User::factory()->create([
            'name' => 'Admin2',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $admin2->assignRole($adminRole);
        $permissions = $adminRole->permissions()->pluck('name')->toArray();
        $admin2->givePermissionTo($permissions);
    }
}
