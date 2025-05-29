<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-classrooms')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $classrooms = Classroom::all();
        $classrooms->load('students', 'schedule', 'events', 'subjects');
        return response()->json([
            'data' => $classrooms->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-classrooms')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
        ]);
        Classroom::create($validatedData);
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Classroom $classroom)
    {
        if (!Auth::user()->hasPermission('view-classrooms')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json([
            'data' => $classroom,
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Classroom $classroom)
    {
        if (!Auth::user()->hasPermission('manage-classrooms')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
        ]);
        $classroom->update($validatedData);
        return response()->json([
            'data' => $classroom,
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Classroom $classroom)
    {
        if (!Auth::user()->hasPermission('manage-classrooms')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $classroom->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}