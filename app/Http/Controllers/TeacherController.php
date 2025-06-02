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
        if (!Auth::user()->hasPermission('manage-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id'],
            'name' => ['sometimes', 'string'],
        ]);
        $teacher->update($validatedData);
        return response()->json([
            'data' => $teacher->toArray(),
            'status' => self::HTTP_OK,
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
