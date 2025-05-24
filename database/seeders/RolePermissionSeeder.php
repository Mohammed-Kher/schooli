<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'System Administrator'
            ],
            [
                'name' => 'Teacher',
                'slug' => 'teacher',
                'description' => 'School Teacher'
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'School Student'
            ],
            [
                'name' => 'Parent',
                'slug' => 'parent',
                'description' => 'Student Parent'
            ]
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Create permissions
        $permissions = [
            // User management
            ['name' => 'View Users', 'slug' => 'view-users'],
            ['name' => 'Create Users', 'slug' => 'create-users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users'],

            // Student management
            ['name' => 'View Students', 'slug' => 'view-students'],
            ['name' => 'Create Students', 'slug' => 'create-students'],
            ['name' => 'Edit Students', 'slug' => 'edit-students'],
            ['name' => 'Delete Students', 'slug' => 'delete-students'],

            // Teacher management
            ['name' => 'View Teachers', 'slug' => 'view-teachers'],
            ['name' => 'Create Teachers', 'slug' => 'create-teachers'],
            ['name' => 'Edit Teachers', 'slug' => 'edit-teachers'],
            ['name' => 'Delete Teachers', 'slug' => 'delete-teachers'],

            // Parent management
            ['name' => 'View Parents', 'slug' => 'view-parents'],
            ['name' => 'Create Parents', 'slug' => 'create-parents'],
            ['name' => 'Edit Parents', 'slug' => 'edit-parents'],
            ['name' => 'Delete Parents', 'slug' => 'delete-parents'],

            // Department management
            ['name' => 'View Departments', 'slug' => 'view-departments'],
            ['name' => 'Create Departments', 'slug' => 'create-departments'],
            ['name' => 'Edit Departments', 'slug' => 'edit-departments'],
            ['name' => 'Delete Departments', 'slug' => 'delete-departments'],

            // Subject management
            ['name' => 'View Subjects', 'slug' => 'view-subjects'],
            ['name' => 'Create Subjects', 'slug' => 'create-subjects'],
            ['name' => 'Edit Subjects', 'slug' => 'edit-subjects'],
            ['name' => 'Delete Subjects', 'slug' => 'delete-subjects'],

            // Homework management
            ['name' => 'View Homework', 'slug' => 'view-homework'],
            ['name' => 'Create Homework', 'slug' => 'create-homework'],
            ['name' => 'Edit Homework', 'slug' => 'edit-homework'],
            ['name' => 'Delete Homework', 'slug' => 'delete-homework'],

            // Attendance management
            ['name' => 'View Attendance', 'slug' => 'view-attendance'],
            ['name' => 'Create Attendance', 'slug' => 'create-attendance'],
            ['name' => 'Edit Attendance', 'slug' => 'edit-attendance'],
            ['name' => 'Delete Attendance', 'slug' => 'delete-attendance'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'description' => $permission['name'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Assign permissions to roles
        $rolePermissions = [
            'admin' => ['*'], // Admin gets all permissions
            'teacher' => [
                'view-students',
                'view-parents',
                'view-departments',
                'view-subjects',
                'view-homework',
                'create-homework',
                'edit-homework',
                'delete-homework',
                // Attendance permissions for teachers
                'view-attendance',
                'create-attendance',
                'edit-attendance',
                'delete-attendance'
            ],
            'student' => [
                'view-departments',
                'view-subjects',
                'view-homework',
                // Students can view their own attendance
                'view-attendance'
            ],
            'parent' => [
                'view-departments',
                'view-subjects',
                'view-homework',
                // Parents can view attendance
                'view-attendance'
            ]
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->first()->id;

            if ($permissionSlugs[0] === '*') {
                // Assign all permissions to admin
                $permissions = DB::table('permissions')->get();
                foreach ($permissions as $permission) {
                    DB::table('permission_role')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permission->id
                    ]);
                }
            } else {
                foreach ($permissionSlugs as $permissionSlug) {
                    $permissionId = DB::table('permissions')
                        ->where('slug', $permissionSlug)
                        ->first()
                        ->id;

                    DB::table('permission_role')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId
                    ]);
                }
            }
        }
    }
}