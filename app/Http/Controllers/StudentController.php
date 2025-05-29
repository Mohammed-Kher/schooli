<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $students = Student::with('parent.user', 'classroom')->get();
        return response()->json([
            'data' => $students->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'parent_id' => ['required', 'exists:parent_students,id'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'gender' => ['required', 'string'],
            'name' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ]);
        $student = Student::create($validatedData);
        return response()->json([
            'data' => $student->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Student $student)
    {
        if (!Auth::user()->hasPermission('view-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $student->load('parent.user', 'classroom');
        return response()->json([
            'data' => $student->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Student $student)
    {
        if (!Auth::user()->hasPermission('manage-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'parent_id' => ['sometimes', 'exists:parent_students,id'],
            'classroom_id' => ['sometimes', 'exists:classrooms,id'],
            'gender' => ['sometimes', 'string'],
            'name' => ['sometimes', 'string'],
            'birth_date' => ['sometimes', 'date'],
        ]);
        $student->update($validatedData);
        return response()->json([
            'data' => $student->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Student $student)
    {
        if (!Auth::user()->hasPermission('manage-students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $student->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}