<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-subjects')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $subjects = Subject::with(['department', 'teacher'])->get();
        return response()->json(['data' => $subjects, 'message' => self::RETRIEVED]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasPermission('create-subjects')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'required|exists:users,id'
        ]);

        Subject::create($validated);
        return response()->json(['message' => self::CREATED], self::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-subjects')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $subject = Subject::with(['department', 'teacher'])->findOrFail($id);
        return response()->json(['data' => $subject, 'message' => self::RETRIEVED]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('edit-subjects')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'department_id' => 'sometimes|exists:departments,id',
            'teacher_id' => 'sometimes|exists:users,id'
        ]);

        $subject->update($validated);
        return response()->json(['message' => self::UPDATED]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('delete-subjects')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(['message' => self::DELETED]);
    }
}
