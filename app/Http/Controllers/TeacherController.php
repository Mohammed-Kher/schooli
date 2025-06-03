<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teachers = Teacher::with('user', 'subjects')->get();
        return response()->json([
            'data' => $teachers->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make(($validatedData['password'])),

        ]);
        $role = DB::table('roles')->where('slug', 'teacher')->first();

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
        
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'name' => $user->name,
        ]);

        $role = DB::table('roles')->where('slug', 'teacher')->first();

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        return response()->json([
            'data' => $teacher->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Teacher $teacher)
    {
        if (!Auth::user()->hasPermission('view-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teacher->load('user', 'subjects');
        return response()->json([
            'data' => $teacher->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        if (!Auth::user()->hasPermission('manage-parent-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if(!$teacher) {
            return response()->json(['error' => 'ParentStudent not found'], 404);
        }
        $user = User::find($teacher->user_id);
        if(!$user) {
            return response()->json(['error' => 'User not found for this Teacher'], 404);
        }

        $validatedData = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);
        $user = $teacher->user;
        // dd($user);
        
        $is_updated = $user->update([
            'name' => $validatedData['name'] ?? $user->name,
            'email' => $validatedData['email'] ?? $user->email,
            'password' => $request->has('password') ? Hash::make($validatedData['password']) : $user->password,
        ]);
        
        return response()->json([
            'data' => $user->toArray(),
            'status' => $is_updated ? self::HTTP_OK : self::NOT_FOUND,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Teacher $teacher)
    {
        if (!Auth::user()->hasPermission('manage-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teacher->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}
