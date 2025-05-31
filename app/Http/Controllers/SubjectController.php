<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-subjects')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $subjects = Subject::with('teacher', 'classroom')->get();
        return response()->json([
            'data' => $subjects->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-subjects')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
        ]);
        $subject = Subject::create($validatedData);
        return response()->json([
            'data' => $subject->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Subject $subject)
    {
        if (!Auth::user()->hasPermission('view-subjects')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $subject->load('teacher', 'classroom');
        return response()->json([
            'data' => $subject->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        if (!Auth::user()->hasPermission('manage-subjects')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'name' => ['sometimes', 'string'],
            'teacher_id' => ['sometimes', 'exists:teachers,id'],
            'classroom_id' => ['sometimes', 'exists:classrooms,id'],
        ]);
        $subject->update($validatedData);
        return response()->json([
            'data' => $subject->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Subject $subject)
    {
        if (!Auth::user()->hasPermission('manage-subjects')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $subject->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}