<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRoles();
        $this->createPermissions();
        $this->assignPermissionsToRoles();
        $this->assignRolesToUsers();
    }
    
    private function createRoles()
    {
        $roles = [
            ['name' => 'المدير', 'slug' => 'admin', 'description' => 'مدير النظام'],
            ['name' => 'المعلم', 'slug' => 'teacher', 'description' => 'معلم المدرسة'],
            ['name' => 'الطالب', 'slug' => 'student', 'description' => 'طالب المدرسة'],
            ['name' => 'الوالد', 'slug' => 'parent', 'description' => 'والد الطالب'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function createPermissions()
    {
        $permissions = [
            ['name' => 'عرض الفصول', 'slug' => 'view-classrooms', 'description' => 'عرض معلومات الفصول'],
            ['name' => 'إدارة الفصول', 'slug' => 'manage-classrooms', 'description' => 'إنشاء وتحديث وحذف الفصول'],
            ['name' => 'عرض الأيام', 'slug' => 'view-days', 'description' => 'عرض أيام الجدول'],
            ['name' => 'إدارة الأيام', 'slug' => 'manage-days', 'description' => 'إنشاء وتحديث وحذف أيام الجدول'],
            ['name' => 'عرض الأحداث', 'slug' => 'view-events', 'description' => 'عرض الأحداث'],
            ['name' => 'إنشاء الأحداث', 'slug' => 'create-events', 'description' => 'إنشاء أحداث جديدة'],
            ['name' => 'تعديل الأحداث', 'slug' => 'edit-events', 'description' => 'تعديل الأحداث الموجودة'],
            ['name' => 'حذف الأحداث', 'slug' => 'delete-events', 'description' => 'حذف الأحداث'],
            ['name' => 'عرض الواجبات', 'slug' => 'view-homework', 'description' => 'عرض الواجبات المنزلية'],
            ['name' => 'إنشاء الواجبات', 'slug' => 'create-homework', 'description' => 'إنشاء واجبات منزلية جديدة'],
            ['name' => 'تعديل الواجبات', 'slug' => 'edit-homework', 'description' => 'تعديل الواجبات المنزلية'],
            ['name' => 'حذف الواجبات', 'slug' => 'delete-homework', 'description' => 'حذف الواجبات المنزلية'],
            ['name' => 'عرض الدروس', 'slug' => 'view-lessons', 'description' => 'عرض جدول الدروس'],
            ['name' => 'إدارة الدروس', 'slug' => 'manage-lessons', 'description' => 'إنشاء وتحديث وحذف الدروس'],
            ['name' => 'عرض أولياء الأمور', 'slug' => 'view-parent_students', 'description' => 'عرض علاقات أولياء الأمور والطلاب'],
            ['name' => 'إدارة أولياء الأمور', 'slug' => 'manage-parent_students', 'description' => 'إنشاء وتحديث وحذف علاقات أولياء الأمور'],
            ['name' => 'عرض الجداول', 'slug' => 'view-schedules', 'description' => 'عرض جداول الفصول'],
            ['name' => 'إدارة الجداول', 'slug' => 'manage-schedules', 'description' => 'إنشاء وتحديث وحذف الجداول'],
            ['name' => 'عرض الطلاب', 'slug' => 'view-students', 'description' => 'عرض معلومات الطلاب'],
            ['name' => 'إدارة الطلاب', 'slug' => 'manage-students', 'description' => 'إنشاء وتحديث وحذف الطلاب'],
            ['name' => 'عرض المواد', 'slug' => 'view-subjects', 'description' => 'عرض معلومات المواد'],
            ['name' => 'إدارة المواد', 'slug' => 'manage-subjects', 'description' => 'إنشاء وتحديث وحذف المواد'],
            ['name' => 'عرض المعلمين', 'slug' => 'view-teachers', 'description' => 'عرض معلومات المعلمين'],
            ['name' => 'إدارة المعلمين', 'slug' => 'manage-teachers', 'description' => 'إنشاء وتحديث وحذف المعلمين'],
            ['name' => 'عرض الحضور', 'slug' => 'view-attendance', 'description' => 'عرض سجلات الحضور'],
            ['name' => 'إدارة الحضور', 'slug' => 'manage-attendance', 'description' => 'إنشاء وتحديث وحذف سجلات الحضور'],
            ['name' => 'إدارة الآباء', 'slug' => 'manage-parent_students', 'description' => 'إدارة الآباء'],
            ['name' => 'عرض الآباء', 'slug' => 'view-parent_students', 'description' => 'عرض الآباء'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'description' => $permission['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function assignPermissionsToRoles()
    {
        // Assign all permissions to admin
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        $allPermissionIds = DB::table('permissions')->pluck('id')->toArray();
        
        foreach ($allPermissionIds as $permissionId) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Assign specific permissions to other roles
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
                'manage-parent_students',
                'view-parent_students',
            ],
            'student' => [
                'view-classrooms',
                'view-days',
                'view-events',
                'view-homework',
                'view-lessons',
                'view-schedules',
                'view-students',
                'view-subjects',
                'view-attendance',
            ],
            'parent' => [
                'view-classrooms',
                'view-days',
                'view-events',
                'view-homework',
                'view-lessons',
                'view-schedules',
                'view-students',
                'view-subjects',
                'view-attendance',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = DB::table('roles')->where('slug', $roleSlug)->first();
            
            foreach ($permissionSlugs as $permissionSlug) {
                $permission = DB::table('permissions')->where('slug', $permissionSlug)->first();
                
                DB::table('permission_role')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
    
    private function assignRolesToUsers()
    {
        $userRoles = [
            ['email' => 'admin@school.com', 'role_slug' => 'admin'],
            ['email' => 'ahmed.teacher@school.com', 'role_slug' => 'teacher'],
            ['email' => 'fatima.teacher@school.com', 'role_slug' => 'teacher'],
            ['email' => 'khalid.parent@school.com', 'role_slug' => 'parent'],
            ['email' => 'noura.parent@school.com', 'role_slug' => 'parent'],
            ['email' => 'abdullah.student@school.com', 'role_slug' => 'student'],
            ['email' => 'sara.student@school.com', 'role_slug' => 'student'],
            ['email' => 'yusuf.student@school.com', 'role_slug' => 'student'],
            ['email' => 'laila.student@school.com', 'role_slug' => 'student'],
            ['email' => 'ali.student@school.com', 'role_slug' => 'student'],
            ['email' => 'test@example.com', 'role_slug' => 'admin'],
        ];

        foreach ($userRoles as $userRole) {
            $user = User::where('email', $userRole['email'])->first();

            if (!$user) {
                echo "User with email {$userRole['email']} not found!\n";
                continue;
            }

            $role = DB::table('roles')->where('slug', $userRole['role_slug'])->first();

            if (!$role) {
                echo "Role with slug {$userRole['role_slug']} not found!\n";
                continue;
            }

            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

            echo "Assigned role {$role->name} to user {$user->email}\n";
        }

        // foreach ($userRoles as $userRole) {
            // $user = User::where('email', $userRole['email'])->first();
            // if ($user) {
                // $roleId = DB::table('roles')->where('slug', $userRole['role_slug'])->first()->id;
                // DB::table('role_user')->insert([
                    // 'user_id' => $user->id,
                    // 'role_id' => $roleId,
                // ]);
            // }
        // }
    }
}
