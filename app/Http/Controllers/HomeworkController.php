<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-homework')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $homeworks = Homework::with('subject', 'teacher')->get();
        return response()->json([
            'data' => $homeworks->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create-homework')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $validatedData = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
        ]);
        $homework = Homework::create(array_merge($validatedData, ['teacher_id' => $teacher->id]));
        return response()->json([
            'data' => $homework->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Homework $homework)
    {
        if (!Auth::user()->hasPermission('view-homework')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $homework->load('subject', 'teacher');
        return response()->json([
            'data' => $homework->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Homework $homework)
    {
        if (!Auth::user()->hasPermission('edit-homework')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'due_date' => ['sometimes', 'date'],
        ]);
        $homework->update($validatedData);
        return response()->json([
            'data' => $homework->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Homework $homework)
    {
        if (!Auth::user()->hasPermission('delete-homework')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $homework->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}