<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-lessons')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $lessons = Lesson::with('day', 'subject')->get();
        return response()->json([
            'data' => $lessons->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-lessons')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'day_id' => ['required', 'exists:days,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
        $lesson = Lesson::create($validatedData);
        return response()->json([
            'data' => $lesson->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Lesson $lesson)
    {
        if (!Auth::user()->hasPermission('view-lessons')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $lesson->load('day', 'subject');
        return response()->json([
            'data' => $lesson->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Lesson $lesson)
    {
        if (!Auth::user()->hasPermission('manage-lessons')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'day_id' => ['sometimes', 'exists:days,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
        ]);
        $lesson->update($validatedData);
        return response()->json([
            'data' => $lesson->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Lesson $lesson)
    {
        if (!Auth::user()->hasPermission('manage-lessons')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $lesson->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}