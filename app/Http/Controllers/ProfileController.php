<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user =  Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // if( auth()->id() !== $user->id) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        return response()->json([
            'data' => new UserResource($user),
            'message' => self::RETRIEVED,
            'status' => self::HTTP_OK,
        ]);
    }

    public function update(Request $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }



        // Validation rules
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
}
