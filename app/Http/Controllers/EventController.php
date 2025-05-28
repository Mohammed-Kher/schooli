<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view-events')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $events = Event::with('subject', 'classroom')->get();
        return response()->json([
            'data' => $events->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create-events')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'type' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);
        $event = Event::create($validatedData);
        return response()->json([
            'data' => $event->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function show(Event $event)
    {
        if (!Auth::user()->hasPermission('view-events')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $event->load('subject', 'classroom');
        return response()->json([
            'data' => $event->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        if (!Auth::user()->hasPermission('edit-events')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validatedData = $request->validate([
            'classroom_id' => ['sometimes', 'exists:classrooms,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'type' => ['sometimes', 'string'],
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ]);
        $event->update($validatedData);
        return response()->json([
            'data' => $event->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Event $event)
    {
        if (!Auth::user()->hasPermission('delete-events')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $event->delete();
        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}