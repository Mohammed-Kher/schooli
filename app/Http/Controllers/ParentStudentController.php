<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\ParentStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ParentStudentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $parents = ParentStudent::with('user')->get();
        return response()->json([
            'data' => $parents->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-parent-students')) {
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
            'password' => Hash::make($validatedData['password']),
        ]);

        $role = DB::table('roles')->where('slug', 'parent')->first();

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);


        $parent = ParentStudent::create(['user_id' => $user->id]);
        return response()->json([
            'data' => $parent->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(ParentStudent $parentStudent)
    {
        if (!Auth::user()->hasPermission('view-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $parentStudent->load('user');
        return response()->json([
            'data' => $parentStudent->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, ParentStudent $parent)
    {
        if (!Auth::user()->hasPermission('manage-parent-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if(!$parent) {
            return response()->json(['error' => 'ParentStudent not found'], 404);
        }
        $user = User::find($parent->user_id);
        if(!$user) {
            return response()->json(['error' => 'User not found for this ParentStudent'], 404);
        }
        $id = $user->id;

        $validatedData = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'name.max' => 'الاسم لا يمكن أن يتجاوز 255 حرفًا.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'email.max' => 'البريد الإلكتروني لا يمكن أن يتجاوز 255 حرفًا.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        ]);

        // Log the request for debugging
        Log::info('Updating user profile', [
            'id' => $id,
            'request_data' => $request->all(),
        ]);


        // Update fields only if provided
        $user->fill([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
            'password' => $request->password ? Hash::make($request->input('password')) : $user->password,
        ]);

        // Save the user
        $user->save();

        // Return the updated user data
        return response()->json([
            'data' => new UserResource($user),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ], self::HTTP_OK);
    }

    public function destroy(ParentStudent $parentStudent)
    {
        if (!Auth::user()->hasPermission('manage-parent-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $parentStudent->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}
