<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if( auth()->id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'data' => $user,
            'message' => self::RETRIEVED,
            'status' => self::HTTP_OK,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if(!$user) {
            return response()->json(['error' => 'User not found for this ParentStudent'], 404);
        }
        if( auth()->id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $validatedData = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

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
}
