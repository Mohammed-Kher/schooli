<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentParentController extends Controller
{
    public function index(): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-parents')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $parents = User::whereHas('roles', function ($q) {
            $q->where('slug', 'parent');
        })->with(['parentProfile', 'children'])->get();

        return response()->json(['data' => $parents, 'message' => self::RETRIEVED]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasPermission('create-parents')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'required|string',
            'address' => 'required|string',
            'children' => 'required|array',
            'children.*' => 'exists:users,id'
        ]);

        // Create parent logic here
        return response()->json(['message' => self::CREATED], self::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-parents')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $parent = User::whereHas('roles', function ($q) {
            $q->where('slug', 'parent');
        })->with(['parentProfile', 'children'])->findOrFail($id);

        return response()->json(['data' => $parent, 'message' => self::RETRIEVED]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('edit-parents')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $parent = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string',
            'children' => 'sometimes|array',
            'children.*' => 'exists:users,id'
        ]);

        $parent->update($validated);
        return response()->json(['message' => self::UPDATED]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('delete-parents')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $parent = User::findOrFail($id);
        $parent->delete();
        return response()->json(['message' => self::DELETED]);
    }
}
