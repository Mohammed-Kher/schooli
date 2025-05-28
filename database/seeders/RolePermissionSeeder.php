<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'System Administrator'],
            ['name' => 'Teacher', 'slug' => 'teacher', 'description' => 'School Teacher'],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'School Student'],
            ['name' => 'Parent', 'slug' => 'parent', 'description' => 'Student Parent'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
            ]);
        }

        // Define permissions for all controller methods, including attendance
        $permissions = [
            // Classroom permissions
            ['name' => 'View Classrooms', 'slug' => 'view-classrooms', 'description' => 'View classroom information'],
            ['name' => 'Manage Classrooms', 'slug' => 'manage-classrooms', 'description' => 'Create, update, and delete classrooms'],
            
            // Day permissions
            ['name' => 'View Days', 'slug' => 'view-days', 'description' => 'View schedule days'],
            ['name' => 'Manage Days', 'slug' => 'manage-days', 'description' => 'Create, update, and delete schedule days'],
            
            // Event permissions
            ['name' => 'View Events', 'slug' => 'view-events', 'description' => 'View events'],
            ['name' => 'Create Events', 'slug' => 'create-events', 'description' => 'Create new events'],
            ['name' => 'Edit Events', 'slug' => 'edit-events', 'description' => 'Edit existing events'],
            ['name' => 'Delete Events', 'slug' => 'delete-events', 'description' => 'Delete events'],
            
            // Homework permissions
            ['name' => 'View Homework', 'slug' => 'view-homework', 'description' => 'View homework assignments'],
            ['name' => 'Create Homework', 'slug' => 'create-homework', 'description' => 'Create new homework assignments'],
            ['name' => 'Edit Homework', 'slug' => 'edit-homework', 'description' => 'Edit existing homework assignments'],
            ['name' => 'Delete Homework', 'slug' => 'delete-homework', 'description' => 'Delete homework assignments'],
            
            // Lesson permissions
            ['name' => 'View Lessons', 'slug' => 'view-lessons', 'description' => 'View lesson schedules'],
            ['name' => 'Manage Lessons', 'slug' => 'manage-lessons', 'description' => 'Create, update, and delete lessons'],
            
            // ParentStudent permissions
            ['name' => 'View Parent Students', 'slug' => 'view-parent_students', 'description' => 'View parent-student relationships'],
            ['name' => 'Manage Parent Students', 'slug' => 'manage-parent_students', 'description' => 'Create, update, and delete parent-student relationships'],
            
            // Schedule permissions
            ['name' => 'View Schedules', 'slug' => 'view-schedules', 'description' => 'View classroom schedules'],
            ['name' => 'Manage Schedules', 'slug' => 'manage-schedules', 'description' => 'Create, update, and delete schedules'],
            
            // Student permissions
            ['name' => 'View Students', 'slug' => 'view-students', 'description' => 'View student information'],
            ['name' => 'Manage Students', 'slug' => 'manage-students', 'description' => 'Create, update, and delete students'],
            
            // Subject permissions
            ['name' => 'View Subjects', 'slug' => 'view-subjects', 'description' => 'View subject information'],
            ['name' => 'Manage Subjects', 'slug' => 'manage-subjects', 'description' => 'Create, update, and delete subjects'],
            
            // Teacher permissions
            ['name' => 'View Teachers', 'slug' => 'view-teachers', 'description' => 'View teacher information'],
            ['name' => 'Manage Teachers', 'slug' => 'manage-teachers', 'description' => 'Create, update, and delete teachers'],
            
            // Attendance permissions
            ['name' => 'View Attendance', 'slug' => 'view-attendance', 'description' => 'View attendance records'],
            ['name' => 'Manage Attendance', 'slug' => 'manage-attendance', 'description' => 'Create, update, and delete attendance records'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'description' => $permission['description'],
            ]);
        }

        // Assign all permissions to Admin
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->first()->id;
        $allPermissionIds = DB::table('permissions')->pluck('id')->toArray();
        foreach ($allPermissionIds as $permissionId) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }

        // Role-specific permissions
        $rolePermissions = [
            'teacher' => [
                'view-classrooms',
                'view-days',
                'view-events',
                'create-events',
                'edit-events',
                'delete-events',
                'view-homework',
                'create-homework',
                'edit-homework',
                'delete-homework',
                'view-lessons',
                'view-schedules',
                'view-students',
                'view-subjects',
                'view-teachers',
                'view-attendance',
                'manage-attendance',
            ],
            'student' => [
                'view-classrooms',
                'view-days',
                'view-events',
                'view-homework',
                'view-lessons',
                'view-schedules',
                'view-students', // Filtered to own info in controller
                'view-subjects',
                'view-attendance', // Filtered to own attendance
            ],
            'parent' => [
                'view-classrooms',
                'view-days',
                'view-events',
                'view-homework',
                'view-lessons',
                'view-schedules',
                'view-students', // Filtered to child's info in controller
                'view-subjects',
                'view-attendance', // Filtered to child's attendance
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->first()->id;
            foreach ($permissionSlugs as $permissionSlug) {
                $permissionId = DB::table('permissions')
                    ->where('slug', $permissionSlug)
                    ->first()
                    ->id;
                DB::table('permission_role')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }
}