<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-schedules')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $schedules = Schedule::with('classroom', 'days')->get();
        return response()->json([
            'data' => $schedules->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-schedules')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
        ]);
        $schedule = Schedule::create($validatedData);
        return response()->json([
            'data' => $schedule->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Schedule $schedule)
    {
        if (!Auth::user()->hasPermission('view-schedules')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $schedule->load('classroom', 'days');
        return response()->json([
            'data' => $schedule->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (!Auth::user()->hasPermission('manage-schedules')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'classroom_id' => ['sometimes', 'exists:classrooms,id'],
        ]);
        $schedule->update($validatedData);
        return response()->json([
            'data' => $schedule->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        if (!Auth::user()->hasPermission('manage-schedules')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $schedule->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}