<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teachers = Teacher::with('user', 'subjects')->get();
        return response()->json([
            'data' => $teachers->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string'],
        ]);
        $teacher = Teacher::create($validatedData);
        return response()->json([
            'data' => $teacher->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Teacher $teacher)
    {
        if (!Auth::user()->hasPermission('view-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teacher->load('user', 'subjects');
        return response()->json([
            'data' => $teacher->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        if (!Auth::user()->hasPermission('manage-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id'],
            'name' => ['sometimes', 'string'],
        ]);
        $teacher->update($validatedData);
        return response()->json([
            'data' => $teacher->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Teacher $teacher)
    {
        if (!Auth::user()->hasPermission('manage-teachers')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $teacher->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}