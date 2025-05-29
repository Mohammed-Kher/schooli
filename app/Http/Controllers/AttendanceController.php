<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermission('view-attendance')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Attendance::with('lesson.subject', 'student');

        if ($request->has('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $attendances = $query->get();

        return response()->json([
            'data' => $attendances->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::RETRIEVED,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('manage-attendance')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $validatedData = $request->validate([
            'lesson_id' => ['required', 'exists:lessons,id'],
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:present,absent,late,excused'],
        ]);

        // Verify teacher is authorized for this lesson (via subject and classroom)
        $lesson = Lesson::with('subject')->findOrFail($validatedData['lesson_id']);
        if ($lesson->subject->teacher_id !== $teacher->id) {
            return response()->json(['error' => 'Unauthorized: You do not teach this lesson'], 403);
        }

        $attendances = [];
        foreach ($validatedData['attendances'] as $attendanceData) {
            $attendances[] = Attendance::create([
                'lesson_id' => $validatedData['lesson_id'],
                'student_id' => $attendanceData['student_id'],
                'status' => $attendanceData['status'],
            ]);
        }

        return response()->json([
            'data' => array_map(fn($attendance) => $attendance->toArray(), $attendances),
            'status' => self::HTTP_OK,
            'message' => self::CREATED,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        if (!Auth::user()->hasPermission('manage-attendance')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $lesson = Lesson::with('subject')->findOrFail($attendance->lesson_id);
        if ($lesson->subject->teacher_id !== $teacher->id) {
            return response()->json(['error' => 'Unauthorized: You do not teach this lesson'], 403);
        }

        $validatedData = $request->validate([
            'status' => ['required', 'in:present,absent,late,excused'],
        ]);

        $attendance->update($validatedData);

        return response()->json([
            'data' => $attendance->toArray(),
            'status' => self::HTTP_OK,
            'message' => self::UPDATED,
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        if (!Auth::user()->hasPermission('manage-attendance')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $lesson = Lesson::with('subject')->findOrFail($attendance->lesson_id);
        if ($lesson->subject->teacher_id !== $teacher->id) {
            return response()->json(['error' => 'Unauthorized: You do not teach this lesson'], 403);
        }

        $attendance->delete();

        return response()->json([
            'data' => null,
            'status' => self::HTTP_OK,
            'message' => self::DELETED,
        ]);
    }
}