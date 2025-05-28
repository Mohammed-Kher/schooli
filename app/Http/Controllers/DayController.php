<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DayController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-days')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $days = Day::with('schedule', 'lessons')->get();
        return response()->json([
            'data' => $days->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-days')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'day' => ['required', 'string'],
        ]);
        $day = Day::create($validatedData);
        return response()->json([
            'data' => $day->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Day $day)
    {
        if (!Auth::user()->hasPermission('view-days')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $day->load('schedule', 'lessons');
        return response()->json([
            'data' => $day->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Day $day)
    {
        if (!Auth::user()->hasPermission('manage-days')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'schedule_id' => ['sometimes', 'exists:schedules,id'],
            'day' => ['sometimes', 'string'],
        ]);
        $day->update($validatedData);
        return response()->json([
            'data' => $day->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Day $day)
    {
        if (!Auth::user()->hasPermission('manage-days')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $day->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}