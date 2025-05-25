<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HomeworkController extends Controller
{
    public function index(): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-homework')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $homework = Homework::with(['subject', 'classroom'])->get();
        return response()->json(['data' => $homework, 'message' => self::RETRIEVED]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasPermission('create-homework')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id'
        ]);

        Homework::create($validated);
        return response()->json(['message' => self::CREATED], self::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('view-homework')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $homework = Homework::with(['subject', 'classroom'])->findOrFail($id);
        return response()->json(['data' => $homework, 'message' => self::RETRIEVED]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('edit-homework')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $homework = Homework::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'due_date' => 'sometimes|date',
            'subject_id' => 'sometimes|exists:subjects,id',
            'classroom_id' => 'sometimes|exists:classrooms,id'
        ]);

        $homework->update($validated);
        return response()->json(['message' => self::UPDATED]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!auth()->user()->hasPermission('delete-homework')) {
            return response()->json(['message' => self::UNAUTHORIZED], self::HTTP_UNAUTHORIZED);
        }

        $homework = Homework::findOrFail($id);
        $homework->delete();
        return response()->json(['message' => self::DELETED]);
    }
}
