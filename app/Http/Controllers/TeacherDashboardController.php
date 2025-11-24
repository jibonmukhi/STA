<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\CourseEvent;
use App\Models\CourseEnrollment;
use App\Models\Certificate;
use App\Models\CourseSession;
use App\Models\SessionAttendance;
use App\Models\User;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Get teacher's courses
        $myCourses = $user->teacherCourses()->with('enrollments')->get();

        // Calculate stats
        $stats = [
            'total_courses' => $myCourses->count(),
            'active_courses' => $myCourses->where('is_active', true)->count(),
            'total_students' => CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->distinct('user_id')
                ->count(),
            'completed_students' => CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
                ->where('status', 'completed')
                ->count(),
            'certificates_issued' => Certificate::whereIn('user_id',
                CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
                    ->pluck('user_id')
            )->count(),
        ];

        // Get upcoming course events
        $upcomingEvents = CourseEvent::whereIn('course_id', $myCourses->pluck('id'))
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        // Get recent enrollments
        $recentEnrollments = CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
            ->with(['user', 'course'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboards.teacher', compact(
            'stats',
            'myCourses',
            'upcomingEvents',
            'recentEnrollments'
        ));
    }

    public function myCourses(Request $request): View
    {
        $user = Auth::user();

        $query = $user->teacherCourses()
            ->withCount('students')
            ->with(['assignedCompanies']);

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, show active courses
            $query->where('status', 'active');
        }

        $perPage = $request->get('per_page', 25);
        $courses = $query->orderBy('title')->paginate($perPage);

        $categories = Course::getCategories();
        $levels = Course::getLevels();

        return view('teacher.my-courses', compact('courses', 'categories', 'levels'));
    }

    public function courseStudents(Request $request, Course $course): View
    {
        $this->authorize('manageStudents', $course);

        $user = Auth::user();

        // Ensure this is teacher's course (check both old teacher_id and new many-to-many relationship)
        $isTeacher = $course->teachers()->where('teacher_id', $user->id)->exists() || $course->teacher_id === $user->id;

        if (!$isTeacher) {
            abort(403, 'Unauthorized access to this course.');
        }

        $query = CourseEnrollment::where('course_id', $course->id)
            ->with(['user', 'company']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->paginate(20);

        $stats = [
            'total_enrolled' => CourseEnrollment::where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->count(),
            'completed' => CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'completed')
                ->count(),
            'in_progress' => CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'in_progress')
                ->count(),
            'average_progress' => CourseEnrollment::where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->avg('progress_percentage') ?? 0,
        ];

        return view('teacher.course-students', compact('course', 'enrollments', 'stats'));
    }

    public function schedule(Request $request): View
    {
        $user = Auth::user();
        $currentMonth = (int) $request->get('month', now()->month);
        $currentYear = (int) $request->get('year', now()->year);

        // Validate month and year parameters
        if ($currentMonth < 1 || $currentMonth > 12) {
            $currentMonth = now()->month;
        }
        if ($currentYear < 1900 || $currentYear > 2100) {
            $currentYear = now()->year;
        }

        // Get course sessions for teacher's courses
        $courseIds = $user->teacherCourses()->pluck('courses.id')->toArray();

        // Get course sessions for current month
        $sessions = CourseSession::query()
            ->with(['course.teachers', 'course.enrollments', 'course.assignedCompanies'])
            ->whereIn('course_id', $courseIds)
            ->where(function($q) use ($currentYear, $currentMonth) {
                $q->whereYear('session_date', $currentYear)
                  ->whereMonth('session_date', $currentMonth);
            })
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Get today's sessions
        $todaysSessions = CourseSession::query()
            ->with(['course.teachers', 'course.enrollments', 'course.assignedCompanies'])
            ->whereIn('course_id', $courseIds)
            ->whereDate('session_date', now()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        // Get upcoming sessions (next 7 days)
        $upcomingSessions = CourseSession::query()
            ->with(['course.teachers', 'course.enrollments', 'course.assignedCompanies'])
            ->whereIn('course_id', $courseIds)
            ->whereDate('session_date', '>=', now()->toDateString())
            ->whereDate('session_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(10)
            ->get();

        // Convert sessions to events
        $events = $sessions;
        $todaysEvents = $todaysSessions;
        $upcomingEvents = $upcomingSessions;

        // Format events for JavaScript (convert CourseSession to event format)
        $formattedEvents = $sessions->map(function($session) {
            $course = $session->course;
            $courseColor = $course->color ?? 'info';
            $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
            $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';

            // Get company names
            $companyNames = $course->assignedCompanies->pluck('name')->join(', ');

            return [
                'id' => $session->id,
                'sessionId' => $session->id,
                'courseId' => $course->id,
                'title' => $course->title . ' - ' . $session->session_title,
                'sessionTitle' => $session->session_title,
                'description' => $session->description ?? $course->description ?? '',
                'date' => $session->session_date->format('Y-m-d'),
                'startDate' => $session->session_date->format('Y-m-d'),
                'endDate' => $session->session_date->format('Y-m-d'),
                'startTime' => Carbon::parse($session->start_time)->format('H:i'),
                'endTime' => Carbon::parse($session->end_time)->format('H:i'),
                'status' => $session->status,
                'sessionStatus' => $session->status,
                'courseStatus' => $course->status,
                'location' => $session->location ?? ($course->delivery_method == 'online' ? 'Online' : ($course->delivery_method == 'offline' ? 'In Presenza' : 'Ibrido')),
                'courseTitle' => $course->title,
                'courseCode' => $course->course_code,
                'instructor' => $course->teachers->pluck('full_name')->join(', ') ?: 'N/A',
                'companyNames' => $companyNames ?: 'N/A',
                'maxParticipants' => $session->max_participants ?? $course->max_participants ?? 0,
                'registeredParticipants' => $course->enrollments->count(),
                'category' => $course->category,
                'courseColor' => $courseColor,
                'categoryColor' => $categoryColor,
                'statusColor' => $statusColor,
                'deliveryMethod' => $course->delivery_method,
                'durationHours' => $session->duration_hours,
                'eventType' => 'course'
            ];
        });

        // Localized month and day names for JavaScript
        $monthNames = [
            trans('calendar.months.january'),
            trans('calendar.months.february'),
            trans('calendar.months.march'),
            trans('calendar.months.april'),
            trans('calendar.months.may'),
            trans('calendar.months.june'),
            trans('calendar.months.july'),
            trans('calendar.months.august'),
            trans('calendar.months.september'),
            trans('calendar.months.october'),
            trans('calendar.months.november'),
            trans('calendar.months.december')
        ];

        $dayNames = [
            trans('calendar.days.sunday'),
            trans('calendar.days.monday'),
            trans('calendar.days.tuesday'),
            trans('calendar.days.wednesday'),
            trans('calendar.days.thursday'),
            trans('calendar.days.friday'),
            trans('calendar.days.saturday')
        ];

        $dayNamesShort = [
            trans('calendar.days_short.sun'),
            trans('calendar.days_short.mon'),
            trans('calendar.days_short.tue'),
            trans('calendar.days_short.wed'),
            trans('calendar.days_short.thu'),
            trans('calendar.days_short.fri'),
            trans('calendar.days_short.sat')
        ];

        // Stats for the calendar sidebar
        $stats = [
            'total_events' => $sessions->count(),
            'todays_events' => $todaysSessions->count(),
            'upcoming_events' => $upcomingSessions->count(),
            'completed_events' => CourseSession::whereIn('course_id', $courseIds)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('teacher.schedule', compact(
            'events',
            'formattedEvents',
            'todaysEvents',
            'upcomingEvents',
            'stats',
            'currentMonth',
            'currentYear',
            'monthNames',
            'dayNames',
            'dayNamesShort'
        ));
    }

    public function showCourse(Course $course): View
    {
        $user = Auth::user();

        // Ensure this is teacher's course (check both old teacher_id and new many-to-many relationship)
        $isTeacher = $course->teachers()->where('teacher_id', $user->id)->exists() || $course->teacher_id === $user->id;

        if (!$isTeacher) {
            abort(403, 'Unauthorized access to this course.');
        }

        // Load relationships
        $course->load([
            'teachers',
            'assignedCompanies',
            'sessions' => function($query) {
                $query->orderBy('session_date', 'asc')->orderBy('start_time', 'asc');
            },
            'materials' => function($query) {
                $query->orderBy('order', 'asc')->orderBy('created_at', 'asc');
            }
        ]);

        // Calculate student statistics
        $stats = [
            'total_enrolled' => CourseEnrollment::where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->count(),
            'completed' => CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'completed')
                ->count(),
            'in_progress' => CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'in_progress')
                ->count(),
            'average_progress' => CourseEnrollment::where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->avg('progress_percentage') ?? 0,
        ];

        return view('teacher.course-details', compact('course', 'stats'));
    }

    public function certificates(Request $request): View
    {
        $user = auth()->user();

        // Get all courses taught by this teacher
        $courseIds = Course::whereHas('teachers', function($q) use ($user) {
            $q->where('users.id', $user->id);
        })->pluck('id');

        // Get certificates for students in teacher's courses
        $certificatesQuery = Certificate::query()
            ->with(['user', 'user.companies'])
            ->whereHas('user.courseEnrollments', function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->where('certificate_type', 'training');

        // Filter by course if specified
        if ($request->has('course_id') && $request->course_id) {
            $certificatesQuery->whereHas('user.courseEnrollments', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by student if specified
        if ($request->has('student_id') && $request->student_id) {
            $certificatesQuery->where('user_id', $request->student_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $certificatesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $certificatesQuery->orderBy('issue_date', 'desc')->paginate(20);

        // Get list of courses for filter dropdown
        $courses = Course::whereIn('id', $courseIds)
            ->orderBy('title')
            ->get();

        // Get list of students for filter dropdown
        $students = User::whereHas('courseEnrollments', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->orderBy('name')->get();

        return view('teacher.certificates', compact('certificates', 'courses', 'students'));
    }

    /**
     * Show session attendance grid for a course
     */
    public function sessionAttendance(Course $course): View
    {
        $this->authorize('markAttendance', $course);

        $user = Auth::user();

        // Load assigned companies
        $course->load('assignedCompanies');

        // Get all sessions for this course
        $sessions = $course->sessions()
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Get all enrolled students
        $enrollments = $course->enrollments()
            ->with(['user', 'attendances'])
            ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
            ->get();

        // Build attendance matrix [enrollment_id][session_id] = attendance
        $attendanceMatrix = [];
        foreach ($enrollments as $enrollment) {
            $attendanceMatrix[$enrollment->id] = [];
            foreach ($sessions as $session) {
                $attendance = $enrollment->attendances()
                    ->where('session_id', $session->id)
                    ->first();

                $attendanceMatrix[$enrollment->id][$session->id] = $attendance;
            }
        }

        return view('teacher.session-attendance', compact('course', 'sessions', 'attendanceMatrix', 'enrollments'));
    }

    /**
     * Show single session attendance detail
     */
    public function showSessionAttendance(CourseSession $session): View
    {
        $this->authorize('markAttendance', $session->course);

        $session->load(['course.assignedCompanies', 'attendances.student']);

        // Get all enrolled students
        $enrollments = $session->course->enrollments()
            ->with('user')
            ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
            ->get();

        // Get attendance stats
        $stats = $session->getAttendanceStats();

        return view('teacher.session-attendance-detail', compact('session', 'enrollments', 'stats'));
    }

    /**
     * Mark attendance for a session
     */
    public function markAttendance(Request $request, CourseSession $session): JsonResponse
    {
        $this->authorize('markAttendance', $session->course);

        $user = Auth::user();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'enrollment_id' => 'required|exists:course_enrollments,id',
            'status' => 'required|in:present,absent,excused,late',
            'attended_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $attendance = SessionAttendance::updateOrCreate(
                [
                    'session_id' => $session->id,
                    'user_id' => $validated['user_id'],
                ],
                [
                    'enrollment_id' => $validated['enrollment_id'],
                    'status' => $validated['status'],
                    'attended_hours' => $validated['attended_hours'] ?? $session->duration_hours,
                    'marked_by' => $user->id,
                    'marked_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            // Trigger recalculation
            $attendance->enrollment->calculateProgressFromAttendance();

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'attendance' => $attendance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk mark attendance for a session
     */
    public function bulkMarkAttendance(Request $request, CourseSession $session): JsonResponse
    {
        $this->authorize('markAttendance', $session->course);

        $user = Auth::user();

        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.enrollment_id' => 'required|exists:course_enrollments,id',
            'attendances.*.status' => 'required|in:present,absent,excused,late',
            'attendances.*.attended_hours' => 'nullable|numeric|min:0',
            'attendances.*.notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['attendances'] as $data) {
                $attendance = SessionAttendance::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'user_id' => $data['user_id'],
                    ],
                    [
                        'enrollment_id' => $data['enrollment_id'],
                        'status' => $data['status'],
                        'attended_hours' => $data['attended_hours'] ?? $session->duration_hours,
                        'marked_by' => $user->id,
                        'marked_at' => now(),
                        'notes' => $data['notes'] ?? null,
                    ]
                );

                // Trigger recalculation
                $attendance->enrollment->calculateProgressFromAttendance();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully for all students',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Close a session
     */
    public function closeSession(CourseSession $session): JsonResponse
    {
        $this->authorize('markAttendance', $session->course);

        if (!$session->canBeClosed()) {
            return response()->json([
                'error' => 'Cannot close session. Not all students have attendance marked.',
                'stats' => $session->getAttendanceStats(),
            ], 400);
        }

        try {
            $session->closeSession();

            return response()->json([
                'success' => true,
                'message' => 'Session closed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate certificates for all enrolled students
     * Marks attendance for remaining sessions and creates certificates
     */
    public function generateCertificates(Course $course)
    {
        $this->authorize('generateCertificates', $course);

        DB::beginTransaction();
        try {
            // 1. Get all non-closed sessions
            $remainingSessions = $course->sessions()
                ->where('status', '!=', 'completed')
                ->get();

            // 2. Get all enrolled students
            $enrollments = $course->enrollments()
                ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
                ->with('user')
                ->get();

            // 3. Mark attendance as present for remaining sessions
            foreach ($remainingSessions as $session) {
                foreach ($enrollments as $enrollment) {
                    SessionAttendance::updateOrCreate(
                        [
                            'session_id' => $session->id,
                            'user_id' => $enrollment->user_id,
                            'enrollment_id' => $enrollment->id
                        ],
                        [
                            'status' => 'present',
                            'attended_hours' => $session->duration_hours,
                            'marked_by' => auth()->id(),
                            'marked_at' => now()
                        ]
                    );
                }
            }

            // 4. Update enrollment status to completed and recalculate progress
            foreach ($enrollments as $enrollment) {
                $enrollment->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                $enrollment->calculateProgressFromAttendance();
            }

            // 5. Delete existing and generate new certificates
            $certificatesGenerated = 0;
            foreach ($enrollments as $enrollment) {
                // Delete existing certificates for this course
                Certificate::where('user_id', $enrollment->user_id)
                    ->where('subject', $course->title)
                    ->delete();

                // Create new certificate
                Certificate::create([
                    'user_id' => $enrollment->user_id,
                    'company_id' => $enrollment->user->company_id ?? null,
                    'name' => $course->title . ' - Certificate of Completion',
                    'subject' => $course->title,
                    'issue_date' => now(),
                    'expiration_date' => now()->addYears(2),
                    'training_organization' => config('app.name', 'Training Organization'),
                    'certificate_type' => 'training',
                    'hours_completed' => $enrollment->attended_hours,
                    'grade' => $enrollment->grade,
                    'status' => 'active',
                    'notes' => 'Course: ' . $course->course_code,
                ]);

                $certificatesGenerated++;
            }

            DB::commit();

            return redirect()->back()->with('success', __('teacher.certificates_generated_success', ['count' => $certificatesGenerated]));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Certificate generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', __('teacher.certificates_generation_failed'));
        }
    }

    /**
     * Display certificates for a specific course
     * Shows all certificates generated for students in a particular course
     */
    public function courseCertificates(Course $course)
    {
        // Check if teacher has access to this course
        $this->authorize('view', $course);

        // Get certificates for this specific course
        $certificates = Certificate::query()
            ->with(['user', 'user.companies'])
            ->whereHas('user.courseEnrollments', function($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('subject', $course->title)
            ->where('certificate_type', 'training')
            ->orderBy('issue_date', 'desc')
            ->paginate(20);

        // Get enrollment statistics
        $stats = [
            'total_enrolled' => $course->enrollments()->count(),
            'certificates_issued' => $certificates->total(),
            'pending_certificates' => $course->enrollments()
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->count(),
            'completion_rate' => $course->enrollments()->count() > 0
                ? round(($certificates->total() / $course->enrollments()->count()) * 100, 1)
                : 0
        ];

        return view('teacher.course-certificates', compact('course', 'certificates', 'stats'));
    }
}
