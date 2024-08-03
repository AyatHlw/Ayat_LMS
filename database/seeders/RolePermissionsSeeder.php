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
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);
        // editable
        $permissions = [
            'user.sign_up', 'user.sign_in', 'user.sign_out', 'instructor.sign_up',
            'user.approve', 'user.block',
            'course.add', 'user.show_pending',
            'course.comment', 'course.rating',
            'course.delete_comment',
            'check.email_password',
            'certificate.get',
            'report.create','report.get', 'report.delete'
            // ..
        ];
        $superAdminPermissions = [
            'user.sign_up', 'user.sign_in', 'user.sign_out',
            'user.approve', 'user.block',
            'check.email_password',

            // ..
        ];
        $adminPermissions = [
            'user.sign_up', 'user.sign_in', 'user.sign_out',
            'user.approve', 'user.block',
            'course.delete_comment',
            'check.email_password',
            'report.get', 'report.delete'
            // ..
        ];
        $teacherPermissions = [
            'user.sign_up', 'user.sign_in', 'user.sign_out',
            'course.add', 'user.block',
            'check.email_password',

            // ..
        ];
        $studentPermissions = [
            'user.sign_up', 'user.sign_in', 'user.sign_out',
            'course.comment', 'course.rating',
            'course.delete_comment',
            'check.email_password',
            'certificate.get',
            'report.create'
            // ..
        ];
        // insert the permissions in database
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdminRole->syncPermissions($superAdminPermissions);
        $adminRole->syncPermissions($adminPermissions);
        $teacherRole->syncPermissions($teacherPermissions);
        $studentRole->syncPermissions($studentPermissions);

        //super Admin
        $superAdmin = User::factory()->create([
            'name' => 'Super_Admin',
            'email' => 'superAdmin@gmail.com',
            'password' => Hash::make('123456789')
        ]);

        $superAdmin->assignRole($superAdminRole);
        $permissions = $superAdminRole->permissions()->pluck('name')->toArray();
        $superAdmin->givePermissionTo($permissions);

        //admin1
        $admin1 = User::factory()->create([
            'name' => 'Admin1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $admin1->assignRole($adminRole);
        $permissions = $adminRole->permissions()->pluck('name')->toArray();
        $admin1->givePermissionTo($permissions);

        //admin2
        $admin2 = User::factory()->create([
            'name' => 'Admin2',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $admin2->assignRole($adminRole);
        $permissions = $adminRole->permissions()->pluck('name')->toArray();
        $admin2->givePermissionTo($permissions);

        //admin amr
        $admin3 = User::factory()->create([
            'name' => 'Admin3',
            'email' => 'aaamr.2012@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $admin3->assignRole($adminRole);
        $permissions = $adminRole->permissions()->pluck('name')->toArray();
        $admin3->givePermissionTo($permissions);

        //teacher ayat
        $teacher1 = User::factory()->create([
            'name' => 'ayat',
            'email' => 'ayat.hlw123@gmail.com',
            'password' => Hash::make('123456789')
        ]);

        //teacher1
        $teacher1 = User::factory()->create([
            'name' => 'teacher1',
            'email' => 'teacher1@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $teacher1->assignRole($teacherRole);
        $permissions = $teacherRole->permissions()->pluck('name')->toArray();
        $teacher1->givePermissionTo($permissions);

        //teacher2
        $teacher2 = User::factory()->create([
            'name' => 'teacher2',
            'email' => 'teacher2@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $teacher2->assignRole($teacherRole);
        $permissions = $teacherRole->permissions()->pluck('name')->toArray();
        $teacher2->givePermissionTo($permissions);

        //teacher3
        $teacher3 = User::factory()->create([
            'name' => 'teacher3',
            'email' => 'teacher3@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $teacher3->assignRole($teacherRole);
        $permissions = $teacherRole->permissions()->pluck('name')->toArray();
        $teacher3->givePermissionTo($permissions);

        //teacher4
        $teacher4 = User::factory()->create([
            'name' => 'teacher4',
            'email' => 'teacher4@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $teacher4->assignRole($teacherRole);
        $permissions = $teacherRole->permissions()->pluck('name')->toArray();
        $teacher4->givePermissionTo($permissions);

        //teacher5
        $teacher5 = User::factory()->create([
            'name' => 'teacher5',
            'email' => 'teacher5@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $teacher5->assignRole($teacherRole);
        $permissions = $teacherRole->permissions()->pluck('name')->toArray();
        $teacher5->givePermissionTo($permissions);


        //student1
        $student1 = User::factory()->create([
            'name' => 'student1',
            'email' => 'student1@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $student1->assignRole($studentRole);
        $permissions = $studentRole->permissions()->pluck('name')->toArray();
        $student1->givePermissionTo($permissions);

        //student2
        $student2 = User::factory()->create([
            'name' => 'student2',
            'email' => 'student2@gmail.com',
            'password' => Hash::make('123456789')
        ]);
        $student2->assignRole($studentRole);
        $permissions = $studentRole->permissions()->pluck('name')->toArray();
        $student2->givePermissionTo($permissions);
    }
}
