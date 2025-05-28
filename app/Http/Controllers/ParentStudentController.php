<?php

namespace App\Http\Controllers;

use App\Models\ParentStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentStudentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $parents = ParentStudent::with('user')->get();
        return response()->json([
            'data' => $parents->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);
        $parent = ParentStudent::create($validatedData);
        return response()->json([
            'data' => $parent->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(ParentStudent $parentStudent)
    {
        if (!Auth::user()->hasPermission('view-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $parentStudent->load('user');
        return response()->json([
            'data' => $parentStudent->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, ParentStudent $parentStudent)
    {
        if (!Auth::user()->hasPermission('manage-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id'],
        ]);
        $parentStudent->update($validatedData);
        return response()->json([
            'data' => $parentStudent->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(ParentStudent $parentStudent)
    {
        if (!Auth::user()->hasPermission('manage-parent_students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $parentStudent->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}