<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeacherController extends Controller
{
    public function index(): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-teachers')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $teachers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'teacher');
        })->with(['teacherProfile', 'subjects'])->get();

        return response()->json(['data' => $teachers, 'message' => self::RETRIEVED]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasPermission('create-teachers')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'department_id' => 'required|exists:departments,id',
            'subjects' => 'array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        // Create teacher logic here
        return response()->json(['message' => self::CREATED], self::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-teachers')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $teacher = User::whereHas('roles', function ($q) {
            $q->where('slug', 'teacher');
        })->with(['teacherProfile', 'subjects'])->findOrFail($id);

        return response()->json(['data' => $teacher, 'message' => self::RETRIEVED]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('edit-teachers')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $teacher = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'department_id' => 'sometimes|exists:departments,id',
            'subjects' => 'sometimes|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        $teacher->update($validated);
        return response()->json(['message' => self::UPDATED]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('delete-teachers')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $teacher = User::findOrFail($id);
        $teacher->delete();
        return response()->json(['message' => self::DELETED]);
    }
}
