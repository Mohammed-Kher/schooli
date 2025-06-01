<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $with = [
        'roles.permissions',
        'teacher.subjects.classroom',
        'parent.students.classroom'
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'birthday',
        'phone',
        'address',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }
    public function getPermissions() {

        return $this->roles()->with('permissions')->get()->pluck('permissions','name','slug')->toArray();
    }

    public function getPermissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions','slug')->toArray();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        return DB::table('role_user')
            ->join('permission_role', 'role_user.role_id', '=', 'permission_role.role_id')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('role_user.user_id', $this->id)
            ->where('permissions.slug', $permissionSlug)
            ->exists();
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(ParentStudent::class);
    }
}