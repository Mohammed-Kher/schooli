<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassroomController extends Controller
{
    public function index(): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-classrooms')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $classrooms = Classroom::with(['department', 'students'])->get();
        return response()->json(['data' => $classrooms, 'message' => self::RETRIEVED]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasPermission('create-classrooms')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'capacity' => 'required|integer|min:1'
        ]);

        Classroom::create($validated);
        return response()->json(['message' => self::CREATED], self::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-classrooms')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $classroom = Classroom::with(['departments', 'students'])->findOrFail($id);
        return response()->json(['data' => $classroom, 'message' => self::RETRIEVED]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('edit-classrooms')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $classroom = Classroom::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'department_id' => 'sometimes|exists:departments,id',
            'capacity' => 'sometimes|integer|min:1'
        ]);

        $classroom->update($validated);
        return response()->json(['message' => self::UPDATED]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('delete-classrooms')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $classroom = Classroom::findOrFail($id);
        $classroom->delete();
        return response()->json(['message' => self::DELETED]);
    }
}
