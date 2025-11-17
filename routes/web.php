<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Public pages
Route::get('/about', fn () => Inertia::render('Public/About'))->name('about');
Route::get('/contact', fn () => Inertia::render('Public/Contact'))->name('contact');
Route::get('/faq', fn () => Inertia::render('Public/FAQ'))->name('faq');
Route::get('/apply', fn () => Inertia::render('Public/TeacherApplication'))->name('teacher-application');
Route::get('/leaderboard', fn () => Inertia::render('Public/TeacherLeaderboard'))->name('teacher-leaderboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/students', fn () => Inertia::render('Students/Index'))
        ->middleware('role:admin,super-admin')
        ->name('students.index');
    Route::get('/students/{student}', function ($student) {
        return Inertia::render('Students/Show', ['studentId' => $student]);
    })
        ->middleware('role:admin,super-admin,teacher')
        ->name('students.show');
    Route::get('/teachers', fn () => Inertia::render('Teachers/Index'))
        ->middleware('role:admin,super-admin')
        ->name('teachers.index');
    Route::get('/teachers/{user}', function ($user) {
        return Inertia::render('Teachers/Show', ['userId' => $user]);
    })
        ->middleware('role:admin,super-admin,teacher')
        ->name('teachers.show');
    Route::get('/teacher-applications', fn () => Inertia::render('TeacherApplications/Index'))
        ->middleware('role:super-admin')
        ->name('teacher-applications.index');
    Route::get('/admins/{user}', function ($user) {
        return Inertia::render('Admins/Show', ['userId' => $user]);
    })
        ->middleware('role:super-admin')
        ->name('admins.show');
    Route::get('/admins', fn () => Inertia::render('Teachers/Index', ['filterRole' => 'admin']))
        ->middleware('role:super-admin')
        ->name('admins.index');
    Route::get('/schedule', fn () => Inertia::render('Schedule/Index'))
        ->name('schedule.index');
    Route::get('/classes', fn () => Inertia::render('Classes/Index'))
        ->name('classes.index');
    Route::get('/classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('Classes/Show', ['classId' => $classSchedule]);
    })
        ->name('classes.show');
    Route::get('/makeup-classes', fn () => Inertia::render('MakeupClasses/Index'))
        ->name('makeup-classes.index');
    Route::get('/makeup-classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('MakeupClasses/Show', ['classId' => $classSchedule]);
    })
        ->name('makeup-classes.show');
    Route::get('/attendance', fn () => Inertia::render('Attendance/Index'))
        ->name('attendance.index');
    Route::get('/attendance/{attendance}', function ($attendance) {
        return Inertia::render('Attendance/Show', ['attendanceId' => $attendance]);
    })
        ->name('attendance.show');
    Route::get('/books', fn () => Inertia::render('Books/Index'))
        ->name('books.index');
    Route::get('/books/{book}', function ($book) {
        return Inertia::render('Books/Show', ['bookId' => $book]);
    })
        ->name('books.show');
    Route::get('/assignments', fn () => Inertia::render('Assignments/Index'))
        ->middleware('role:admin,super-admin')
        ->name('assignments.index');
    Route::get('/assignments/{bookAssignment}', function ($bookAssignment) {
        return Inertia::render('Assignments/Show', ['assignmentId' => $bookAssignment]);
    })
        ->middleware('role:admin,super-admin')
        ->name('assignments.show');
    Route::get('/recordings', fn () => Inertia::render('Recordings/Index'))
        ->name('recordings.index');
    Route::get('/recordings/{recording}', function ($recording) {
        return Inertia::render('Recordings/Show', ['recordingId' => $recording]);
    })
        ->name('recordings.show');
    Route::get('/screenshots', fn () => Inertia::render('Screenshots/Index'))
        ->name('screenshots.index');
    Route::get('/screenshots/{screenshot}', function ($screenshot) {
        return Inertia::render('Screenshots/Show', ['screenshotId' => $screenshot]);
    })
        ->name('screenshots.show');
    Route::get('/reports', fn () => Inertia::render('Reports/Index'))
        ->name('reports.index');
    Route::get('/search', fn () => Inertia::render('Search/Index'))
        ->name('search.index');
    Route::get('/messages', fn () => Inertia::render('Messages/Index'))
        ->name('messages.index');
    Route::get('/notifications', fn () => Inertia::render('Notifications/Index'))
        ->name('notifications.index');
    Route::get('/payouts', fn () => Inertia::render('Payouts/Index'))
        ->middleware('role:super-admin')
        ->name('payouts.index');
    Route::get('/payouts/{payout}', function ($payout) {
        return Inertia::render('Payouts/Show', ['payoutId' => $payout]);
    })
        ->middleware('role:super-admin')
        ->name('payouts.show');
    Route::get('/salary', fn () => Inertia::render('Salary/Index'))
        ->middleware('role:super-admin')
        ->name('salary.index');
    Route::get('/curriculum', fn () => Inertia::render('Curriculum/Index'))
        ->middleware('role:super-admin')
        ->name('curriculum.index');
    Route::get('/settings', fn () => Inertia::render('Settings/Index'))
        ->middleware('role:super-admin')
        ->name('settings.index');

    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');





    // ============================================================================
// TEACHER ROUTES - /teacher/*
// ============================================================================
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Teacher/Dashboard'))->name('dashboard');
    
    // Students
    Route::get('/students', fn () => Inertia::render('Teacher/Students/Index'))->name('students.index');
    Route::get('/students/{student}', function ($student) {
        return Inertia::render('Teacher/Students/Show', ['studentId' => $student]);
    })->name('students.show');
    
    // Schedule
    Route::get('/schedule', fn () => Inertia::render('Teacher/Schedule/Index'))->name('schedule.index');
    
    // Classes
    Route::get('/classes', fn () => Inertia::render('Teacher/Classes/Index'))->name('classes.index');
    Route::get('/classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('Teacher/Classes/Show', ['classId' => $classSchedule]);
    })->name('classes.show');
    
    // Makeup Classes
    Route::get('/makeup-classes', fn () => Inertia::render('Teacher/MakeupClasses/Index'))->name('makeup-classes.index');
    Route::get('/makeup-classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('Teacher/MakeupClasses/Show', ['classId' => $classSchedule]);
    })->name('makeup-classes.show');
    
    // Attendance
    Route::get('/attendance', fn () => Inertia::render('Teacher/Attendance/Index'))->name('attendance.index');
    Route::get('/attendance/mark', fn () => Inertia::render('Teacher/Attendance/Mark'))->name('attendance.mark');
    Route::get('/attendance/{attendance}', function ($attendance) {
        return Inertia::render('Teacher/Attendance/Show', ['attendanceId' => $attendance]);
    })->name('attendance.show');
    
    // Books
    Route::get('/books', fn () => Inertia::render('Teacher/Books/Index'))->name('books.index');
    Route::get('/books/{book}', function ($book) {
        return Inertia::render('Teacher/Books/Show', ['bookId' => $book]);
    })->name('books.show');
    
    // Recordings
    Route::get('/recordings', fn () => Inertia::render('Teacher/Recordings/Index'))->name('recordings.index');
    Route::get('/recordings/upload', fn () => Inertia::render('Teacher/Recordings/Upload'))->name('recordings.upload');
    Route::get('/recordings/{recording}', function ($recording) {
        return Inertia::render('Teacher/Recordings/Show', ['recordingId' => $recording]);
    })->name('recordings.show');
    
    // Screenshots
    Route::get('/screenshots', fn () => Inertia::render('Teacher/Screenshots/Index'))->name('screenshots.index');
    Route::get('/screenshots/{screenshot}', function ($screenshot) {
        return Inertia::render('Teacher/Screenshots/Show', ['screenshotId' => $screenshot]);
    })->name('screenshots.show');
    
    // Salary
    Route::get('/salary', fn () => Inertia::render('Teacher/Salary/Index'))->name('salary.index');
});

// ============================================================================
// ADMIN ROUTES - /admin/*
// ============================================================================
Route::middleware(['auth', 'role:admin,super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');
    
    // Teachers Management
    Route::get('/teachers', fn () => Inertia::render('Admin/Teachers/Index'))->name('teachers.index');
    Route::get('/teachers/create', fn () => Inertia::render('Admin/Teachers/Create'))->name('teachers.create');
    Route::get('/teachers/{user}', function ($user) {
        return Inertia::render('Admin/Teachers/Show', ['userId' => $user]);
    })->name('teachers.show');
    Route::get('/teachers/{user}/edit', function ($user) {
        return Inertia::render('Admin/Teachers/Edit', ['userId' => $user]);
    })->name('teachers.edit');
    
    // Students Management
    Route::get('/students', fn () => Inertia::render('Admin/Students/Index'))->name('students.index');
    Route::get('/students/create', fn () => Inertia::render('Admin/Students/Create'))->name('students.create');
    Route::get('/students/{student}', function ($student) {
        return Inertia::render('Admin/Students/Show', ['studentId' => $student]);
    })->name('students.show');
    Route::get('/students/{student}/edit', function ($student) {
        return Inertia::render('Admin/Students/Edit', ['studentId' => $student]);
    })->name('students.edit');
    
    // Classes Management
    Route::get('/classes', fn () => Inertia::render('Admin/Classes/Index'))->name('classes.index');
    Route::get('/classes/create', fn () => Inertia::render('Admin/Classes/Create'))->name('classes.create');
    Route::get('/classes/schedule', fn () => Inertia::render('Admin/Classes/Schedule'))->name('classes.schedule');
    Route::get('/classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('Admin/Classes/Show', ['classId' => $classSchedule]);
    })->name('classes.show');
    
    // Makeup Classes
    Route::get('/makeup-classes', fn () => Inertia::render('Admin/MakeupClasses/Index'))->name('makeup-classes.index');
    Route::get('/makeup-classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('Admin/MakeupClasses/Show', ['classId' => $classSchedule]);
    })->name('makeup-classes.show');
    
    // Attendance
    Route::get('/attendance', fn () => Inertia::render('Admin/Attendance/Index'))->name('attendance.index');
    Route::get('/attendance/reports', fn () => Inertia::render('Admin/Attendance/Reports'))->name('attendance.reports');
    Route::get('/attendance/{attendance}', function ($attendance) {
        return Inertia::render('Admin/Attendance/Show', ['attendanceId' => $attendance]);
    })->name('attendance.show');
    
    // Books Management
    Route::get('/books', fn () => Inertia::render('Admin/Books/Index'))->name('books.index');
    Route::get('/books/{book}', function ($book) {
        return Inertia::render('Admin/Books/Show', ['bookId' => $book]);
    })->name('books.show');
    Route::get('/books/manage', fn () => Inertia::render('Admin/Books/Manage'))->name('books.manage');
    
    // Assignments
    Route::get('/assignments', fn () => Inertia::render('Admin/Assignments/Index'))->name('assignments.index');
    Route::get('/assignments/create', fn () => Inertia::render('Admin/Assignments/Create'))->name('assignments.create');
    Route::get('/assignments/{bookAssignment}', function ($bookAssignment) {
        return Inertia::render('Admin/Assignments/Show', ['assignmentId' => $bookAssignment]);
    })->name('assignments.show');
    
    // Reports
    Route::get('/reports', fn () => Inertia::render('Admin/Reports/Index'))->name('reports.index');
    Route::get('/reports/teacher-performance', fn () => Inertia::render('Admin/Reports/TeacherPerformance'))->name('reports.teacher-performance');
    Route::get('/reports/student-progress', fn () => Inertia::render('Admin/Reports/StudentProgress'))->name('reports.student-progress');
    Route::get('/reports/attendance', fn () => Inertia::render('Admin/Reports/Attendance'))->name('reports.attendance');
    
    // Recordings
    Route::get('/recordings', fn () => Inertia::render('Admin/Recordings/Index'))->name('recordings.index');
    Route::get('/recordings/{recording}', function ($recording) {
        return Inertia::render('Admin/Recordings/Show', ['recordingId' => $recording]);
    })->name('recordings.show');
    
    // Screenshots
    Route::get('/screenshots', fn () => Inertia::render('Admin/Screenshots/Index'))->name('screenshots.index');
    Route::get('/screenshots/{screenshot}', function ($screenshot) {
        return Inertia::render('Admin/Screenshots/Show', ['screenshotId' => $screenshot]);
    })->name('screenshots.show');
    
    // Payouts
    Route::get('/payouts', fn () => Inertia::render('Admin/Payouts/Index'))->name('payouts.index');
    Route::get('/payouts/{payout}', function ($payout) {
        return Inertia::render('Admin/Payouts/Show', ['payoutId' => $payout]);
    })->name('payouts.show');
});

// ============================================================================
// SUPER ADMIN ROUTES - /super-admin/*
// ============================================================================
Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('SuperAdmin/Dashboard'))->name('dashboard');
    
    // Admins Management
    Route::get('/admins', fn () => Inertia::render('SuperAdmin/Admins/Show'))->name('admins.index');
    Route::get('/admins/create', fn () => Inertia::render('SuperAdmin/Admins/Create'))->name('admins.create');
    Route::get('/admins/{user}', function ($user) {
        return Inertia::render('SuperAdmin/Admins/Show', ['userId' => $user]);
    })->name('admins.show');
    Route::get('/admins/manage', fn () => Inertia::render('SuperAdmin/Admins/Manage'))->name('admins.manage');
    
    // Teacher Applications
    Route::get('/teacher-applications', fn () => Inertia::render('SuperAdmin/TeacherApplications/Index'))->name('teacher-applications.index');
    Route::get('/teacher-applications/{application}/review', function ($application) {
        return Inertia::render('SuperAdmin/TeacherApplications/Review', ['applicationId' => $application]);
    })->name('teacher-applications.review');
    
    // Teachers Overview
    Route::get('/teachers', fn () => Inertia::render('SuperAdmin/Teachers/Index'))->name('teachers.index');
    Route::get('/teachers/{user}', function ($user) {
        return Inertia::render('SuperAdmin/Teachers/Show', ['userId' => $user]);
    })->name('teachers.show');
    
    // Students Overview
    Route::get('/students', fn () => Inertia::render('SuperAdmin/Students/Index'))->name('students.index');
    Route::get('/students/{student}', function ($student) {
        return Inertia::render('SuperAdmin/Students/Show', ['studentId' => $student]);
    })->name('students.show');
    
    // Classes
    Route::get('/classes', fn () => Inertia::render('SuperAdmin/Classes/Index'))->name('classes.index');
    Route::get('/classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('SuperAdmin/Classes/Show', ['classId' => $classSchedule]);
    })->name('classes.show');
    
    // Makeup Classes
    Route::get('/makeup-classes', fn () => Inertia::render('SuperAdmin/MakeupClasses/Index'))->name('makeup-classes.index');
    Route::get('/makeup-classes/{classSchedule}', function ($classSchedule) {
        return Inertia::render('SuperAdmin/MakeupClasses/Show', ['classId' => $classSchedule]);
    })->name('makeup-classes.show');
    
    // Attendance
    Route::get('/attendance', fn () => Inertia::render('SuperAdmin/Attendance/Index'))->name('attendance.index');
    Route::get('/attendance/{attendance}', function ($attendance) {
        return Inertia::render('SuperAdmin/Attendance/Show', ['attendanceId' => $attendance]);
    })->name('attendance.show');
    
    // Books
    Route::get('/books', fn () => Inertia::render('SuperAdmin/Books/Index'))->name('books.index');
    Route::get('/books/{book}', function ($book) {
        return Inertia::render('SuperAdmin/Books/Show', ['bookId' => $book]);
    })->name('books.show');
    
    // Assignments
    Route::get('/assignments', fn () => Inertia::render('SuperAdmin/Assignments/Index'))->name('assignments.index');
    Route::get('/assignments/{bookAssignment}', function ($bookAssignment) {
        return Inertia::render('SuperAdmin/Assignments/Show', ['assignmentId' => $bookAssignment]);
    })->name('assignments.show');
    
    // Recordings
    Route::get('/recordings', fn () => Inertia::render('SuperAdmin/Recordings/Index'))->name('recordings.index');
    Route::get('/recordings/{recording}', function ($recording) {
        return Inertia::render('SuperAdmin/Recordings/Show', ['recordingId' => $recording]);
    })->name('recordings.show');
    
    // Screenshots
    Route::get('/screenshots', fn () => Inertia::render('SuperAdmin/Screenshots/Index'))->name('screenshots.index');
    Route::get('/screenshots/{screenshot}', function ($screenshot) {
        return Inertia::render('SuperAdmin/Screenshots/Show', ['screenshotId' => $screenshot]);
    })->name('screenshots.show');
    
    // System Management
    Route::get('/settings', fn () => Inertia::render('SuperAdmin/Settings/Index'))->name('settings.index');
    Route::get('/system/logs', fn () => Inertia::render('SuperAdmin/System/Logs'))->name('system.logs');
    Route::get('/system/analytics', fn () => Inertia::render('SuperAdmin/System/Analytics'))->name('system.analytics');
    
    // Curriculum Management
    Route::get('/curriculum', fn () => Inertia::render('SuperAdmin/Curriculum/Index'))->name('curriculum.index');
    Route::get('/curriculum/manage', fn () => Inertia::render('SuperAdmin/Curriculum/Manage'))->name('curriculum.manage');
    
    // Payouts Processing
    Route::get('/payouts', fn () => Inertia::render('SuperAdmin/Payouts/Index'))->name('payouts.index');
    Route::get('/payouts/process', fn () => Inertia::render('SuperAdmin/Payouts/Process'))->name('payouts.process');
    Route::get('/payouts/history', fn () => Inertia::render('SuperAdmin/Payouts/History'))->name('payouts.history');
    Route::get('/payouts/{payout}', function ($payout) {
        return Inertia::render('SuperAdmin/Payouts/Show', ['payoutId' => $payout]);
    })->name('payouts.show');
    
    // Salary Management
    Route::get('/salary', fn () => Inertia::render('SuperAdmin/Salary/Index'))->name('salary.index');
    
    // Reports
    Route::get('/reports', fn () => Inertia::render('SuperAdmin/Reports/Index'))->name('reports.index');
    Route::get('/reports/overview', fn () => Inertia::render('SuperAdmin/Reports/Overview'))->name('reports.overview');
    Route::get('/reports/financial', fn () => Inertia::render('SuperAdmin/Reports/Financial'))->name('reports.financial');
    Route::get('/reports/system-health', fn () => Inertia::render('SuperAdmin/Reports/SystemHealth'))->name('reports.system-health');
    
    // Global Search
    Route::get('/search', fn () => Inertia::render('SuperAdmin/Search/Index'))->name('search.index');
});

// ============================================================================
// SHARED ROUTES (All authenticated users)
// ============================================================================
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications
    Route::get('/notifications', fn () => Inertia::render('Shared/Notifications/Index'))->name('notifications.index');
    
    // Messages
    Route::get('/messages', fn () => Inertia::render('Shared/Messages/Index'))->name('messages.index');
});
});

require __DIR__.'/auth.php';
