<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Day;
use App\Models\Event;
use App\Models\Homework;
use App\Models\Lesson;
use App\Models\ParentStudent;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users with Arabic Names
        $users = [
            // Admin
            [
                'name' => 'محمد المدير',
                'email' => 'admin@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birthday' => '1980-01-01',
                'phone' => '050-123-4567',
                'address' => 'الرياض، شارع الإدارة',
                'image' => null,
            ],
            // Teachers
            [
                'name' => 'أحمد المعلم',
                'email' => 'ahmed.teacher@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birthday' => '1975-06-15',
                'phone' => '050-234-5678',
                'address' => 'جدة، شارع المعلمين',
                'image' => null,
            ],
            [
                'name' => 'فاطمة المعلمة',
                'email' => 'fatima.teacher@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'female',
                'birthday' => '1982-03-22',
                'phone' => '050-345-6789',
                'address' => 'الدمام، حي المعلمات',
                'image' => null,
            ],
            // Parents
            [
                'name' => 'خالد الوالد',
                'email' => 'khalid.parent@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birthday' => '1978-11-10',
                'phone' => '050-456-7890',
                'address' => 'الرياض، حي الأسرة',
                'image' => null,
            ],
            [
                'name' => 'نورة الوالدة',
                'email' => 'noura.parent@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'female',
                'birthday' => '1976-09-05',
                'phone' => '050-567-8901',
                'address' => 'مكة، شارع الوالدين',
                'image' => null,
            ],
            // Students
            [
                'name' => 'عبدالله الطالب',
                'email' => 'abdullah.student@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birthday' => '2010-04-12',
                'phone' => '050-678-9012',
                'address' => 'الرياض، حي الأسرة',
                'image' => null,
            ],
            [
                'name' => 'سارة الطالبة',
                'email' => 'sara.student@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'female',
                'birthday' => '2010-07-19',
                'phone' => '050-789-0123',
                'address' => 'الرياض، حي الأسرة',
                'image' => null,
            ],
            [
                'name' => 'يوسف الطالب',
                'email' => 'yusuf.student@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birthday' => '2011-02-25',
                'phone' => '050-890-1234',
                'address' => 'مكة، شارع الوالدين',
                'image' => null,
            ],
            [
                'name' => 'ليلى الطالبة',
                'email' => 'laila.student@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'female',
                'birthday' => '2010-12-30',
                'phone' => '050-901-2345',
                'address' => 'مكة، شارع الوالدين',
                'image' => null,
            ],
            [
                'name' => 'علي الطالب',
                'email' => 'ali.student@school.com',
                'password' => Hash::make('12345678'),
                'gender' => 'male',
                'birthday' => '2011-05-15',
                'phone' => '050-012-3456',
                'address' => 'مكة، شارع الوالدين',
                'image' => null,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create(array_merge($userData, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Create Teacher or ParentStudent records
            if (str_contains($userData['email'], 'teacher')) {
                Teacher::create([
                    'user_id' => $user->id,
                    'name' => $userData['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif (str_contains($userData['email'], 'parent')) {
                ParentStudent::create([
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Create Classrooms
        $classrooms = [
            ['name' => 'الصف الأول'],
            ['name' => 'الصف الثاني'],
            ['name' => 'الصف الثالث'],
            ['name' => 'الصف الرابع'],
        ];

        foreach ($classrooms as $classroomData) {
            Classroom::create(array_merge($classroomData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. Create Schedules
        $classrooms = Classroom::all();
        foreach ($classrooms as $classroom) {
            Schedule::create([
                'classroom_id' => $classroom->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Create Days
        $schedules = Schedule::all();
        $daysData = [
            Carbon::today()->startOfWeek()->format('Y-m-d'), // الإثنين
            Carbon::today()->startOfWeek()->addDay()->format('Y-m-d'), // الثلاثاء
            Carbon::today()->startOfWeek()->addDays(2)->format('Y-m-d'), // الأربعاء
            Carbon::today()->startOfWeek()->addDays(3)->format('Y-m-d'), // الخميس
            Carbon::today()->startOfWeek()->addDays(4)->format('Y-m-d'), // الجمعة
        ];

        foreach ($schedules as $schedule) {
            foreach ($daysData as $dayDate) {
                Day::create([
                    'schedule_id' => $schedule->id,
                    'day' => $dayDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 5. Create Subjects
        $teachers = Teacher::all();
        $classrooms = Classroom::all();
        $subjectsData = [
            ['name' => 'الرياضيات', 'teacher_id' => $teachers[0]->id, 'classroom_id' => $classrooms[0]->id],
            ['name' => 'اللغة العربية', 'teacher_id' => $teachers[1]->id, 'classroom_id' => $classrooms[0]->id],
            ['name' => 'العلوم', 'teacher_id' => $teachers[0]->id, 'classroom_id' => $classrooms[1]->id],
            ['name' => 'التاريخ', 'teacher_id' => $teachers[1]->id, 'classroom_id' => $classrooms[1]->id],
        ];

        foreach ($subjectsData as $subjectData) {
            Subject::create(array_merge($subjectData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 6. Create Students
        $parentStudents = ParentStudent::all();
        $studentUsers = User::whereIn('email', [
            'abdullah.student@school.com',
            'sara.student@school.com',
            'yusuf.student@school.com',
            'laila.student@school.com',
            'ali.student@school.com',
        ])->get();

        $studentAssignments = [
            ['user' => $studentUsers[0], 'parent' => $parentStudents[0], 'classroom' => $classrooms[0]], // عبدالله
            ['user' => $studentUsers[1], 'parent' => $parentStudents[0], 'classroom' => $classrooms[0]], // سارة
            ['user' => $studentUsers[2], 'parent' => $parentStudents[1], 'classroom' => $classrooms[0]], // يوسف
            ['user' => $studentUsers[3], 'parent' => $parentStudents[1], 'classroom' => $classrooms[1]], // ليلى
            ['user' => $studentUsers[4], 'parent' => $parentStudents[1], 'classroom' => $classrooms[1]], // علي
        ];

        foreach ($studentAssignments as $assignment) {
            Student::create([
                'parent_id' => $assignment['parent']->id,
                'classroom_id' => $assignment['classroom']->id,
                'name' => $assignment['user']->name,
                'gender' => $assignment['user']->gender,
                'birth_date' => $assignment['user']->birthday,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Create Lessons
        $days = Day::all();
        $lessonTimes = [
            ['start_time' => '09:00', 'end_time' => '10:00'],
            ['start_time' => '10:15', 'end_time' => '11:15'],
        ];
        $subjects = Subject::all();
        foreach ($days as $day) {
            foreach ($subjects as $index => $subject) {
                $time = $lessonTimes[$index % 2];
                Lesson::create([
                    'day_id' => $day->id,
                    'subject_id' => $subject->id,
                    'start_time' => $time['start_time'],
                    'end_time' => $time['end_time'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 8. Create Homework
        $teachers = Teacher::all();
        foreach ($subjects as $subject) {
            $teacher = $teachers->where('id', $subject->teacher_id)->first();
            if ($teacher) {
                Homework::create([
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'name' => "واجب {$subject->name}",
                    'description' => "إكمال التمارين الخاصة بـ {$subject->name}",
                    'due_date' => now()->addDays(7)->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 9. Create Events
        foreach ($classrooms as $classroom) {
            $classroomSubjects = Subject::where('classroom_id', $classroom->id)->get();
            foreach ($classroomSubjects as $subject) {
                Event::create([
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subject->id,
                    'type' => 'اختبار',
                    'title' => "اختبار {$subject->name}",
                    'description' => "اختبار حول مواضيع {$subject->name}",
                    'start_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
                    'end_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 10. Create Attendance
        $lessons = Lesson::all();
        $statuses = ['حاضر', 'غائب', 'متأخر', 'معتذر'];

        foreach ($lessons as $lesson) {
            $subject = Subject::find($lesson->subject_id);
            $classroom = Classroom::find($subject->classroom_id);
            $classroomStudents = Student::where('classroom_id', $classroom->id)->get();

            foreach ($classroomStudents as $student) {
                Attendance::create([
                    'lesson_id' => $lesson->id,
                    'student_id' => $student->id,
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}