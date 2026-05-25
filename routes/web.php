<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isAdmin())
        return redirect()->route('admin.dashboard');
    if ($user->isProfessor())
        return redirect()->route('professor.dashboard');
    if ($user->isStudent())
        return redirect()->route('student.dashboard');

    $totalUsers = \App\Models\User::count();
    $activeModules = \App\Models\Module::count();
    $availableRooms = \App\Models\Room::count();

    return view('dashboard', compact('totalUsers', 'activeModules', 'availableRooms'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Importation Excel/CSV
    Route::get('/users/import', [\App\Http\Controllers\Admin\UserController::class, 'showImportForm'])->name('users.import.form');
    Route::post('/users/import', [\App\Http\Controllers\Admin\UserController::class, 'importUsers'])->name('users.import');
    Route::get('/users/import/template/{type}', [\App\Http\Controllers\Admin\UserController::class, 'downloadTemplate'])->name('users.import.template');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Importation Modules Excel/CSV
    Route::get('/modules/import', [\App\Http\Controllers\Admin\ModuleController::class, 'showImportForm'])->name('modules.import.form');
    Route::post('/modules/import', [\App\Http\Controllers\Admin\ModuleController::class, 'importModules'])->name('modules.import');
    Route::get('/modules/import/template', [\App\Http\Controllers\Admin\ModuleController::class, 'downloadTemplate'])->name('modules.import.template');

    Route::resource('modules', \App\Http\Controllers\Admin\ModuleController::class);

    // Importation Groupes Excel/CSV
    Route::get('/groups/import', [\App\Http\Controllers\Admin\GroupController::class, 'showImportForm'])->name('groups.import.form');
    Route::post('/groups/import', [\App\Http\Controllers\Admin\GroupController::class, 'importGroups'])->name('groups.import');
    Route::get('/groups/import/template', [\App\Http\Controllers\Admin\GroupController::class, 'downloadTemplate'])->name('groups.import.template');

    Route::resource('groups', \App\Http\Controllers\Admin\GroupController::class);

    // Importation Salles Excel/CSV
    Route::get('/rooms/import', [\App\Http\Controllers\Admin\RoomController::class, 'showImportForm'])->name('rooms.import.form');
    Route::post('/rooms/import', [\App\Http\Controllers\Admin\RoomController::class, 'importRooms'])->name('rooms.import');
    Route::get('/rooms/import/template', [\App\Http\Controllers\Admin\RoomController::class, 'downloadTemplate'])->name('rooms.import.template');

    Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class);

    // Academic Year & Assignments
    Route::get('academic', [App\Http\Controllers\Admin\AcademicYearController::class, 'index'])->name('academic.index');
    Route::post('academic/year', [App\Http\Controllers\Admin\AcademicYearController::class, 'storeYear'])->name('academic.year.store');
    Route::patch('academic/year/{year}/current', [App\Http\Controllers\Admin\AcademicYearController::class, 'setCurrentYear'])->name('academic.year.current');
    Route::patch('academic/year/{year}/exam-period', [App\Http\Controllers\Admin\AcademicYearController::class, 'setExamSessions'])->name('academic.year.exam-period');
    Route::post('academic/assignment', [App\Http\Controllers\Admin\AcademicYearController::class, 'storeAssignment'])->name('academic.assignment.store');
    Route::delete('academic/assignment/{assignment}', [App\Http\Controllers\Admin\AcademicYearController::class, 'destroyAssignment'])->name('academic.assignment.destroy');
    Route::get('academic/assignments/export', [App\Http\Controllers\Admin\AcademicYearController::class, 'exportAssignments'])->name('academic.assignments.export');
    Route::post('academic/assignments/import', [App\Http\Controllers\Admin\AcademicYearController::class, 'importAssignments'])->name('academic.assignments.import');
    Route::get('academic/assignments/template', [App\Http\Controllers\Admin\AcademicYearController::class, 'downloadTemplate'])->name('academic.assignments.template');
    
    // Admin Exams
    Route::post('/exams/auto-schedule', [\App\Http\Controllers\Admin\ExamController::class, 'autoSchedule'])->name('exams.auto-schedule');
    Route::post('/exams/{exam}/generate-convocations', [\App\Http\Controllers\Admin\ExamController::class, 'generateConvocations'])->name('exams.generate_convocations');
    Route::post('/exams/{exam}/send-emails', [\App\Http\Controllers\Admin\ExamController::class, 'sendEmails'])->name('exams.send_emails');
    Route::get('/exams/{exam}/pdf', [\App\Http\Controllers\Admin\ExamController::class, 'generatePdf'])->name('exams.pdf');
    Route::get('/exams/{exam}/attendance-sheet', [\App\Http\Controllers\Admin\ExamController::class, 'attendanceSheet'])->name('exams.attendance_sheet');
    // Calendar view for exams filtered by filière
    Route::get('/exams/calendar', [\App\Http\Controllers\Admin\ExamController::class, 'showCalendar'])->name('exams.calendar');
    // Auto‑generate exams for a filière & session
    Route::post('/exams/auto-generate', [\App\Http\Controllers\Admin\ExamController::class, 'autoGenerate'])->name('exams.auto_generate');
    // API endpoint providing events for FullCalendar
    Route::get('/exams/api/calendar', [\App\Http\Controllers\Admin\ExamController::class, 'calendarData'])->name('exams.api.calendar');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);

    // Convocations Scanner (for proctors/admins)
    Route::get('/convocations/{reference}/verify', [\App\Http\Controllers\Admin\ExamController::class, 'verifyConvocation'])->name('convocations.verify');
    Route::post('/convocations/{reference}/present', [\App\Http\Controllers\Admin\ExamController::class, 'markConvocationPresent'])->name('convocations.mark_present');

    Route::resource('schedules', \App\Http\Controllers\ScheduleController::class);
    Route::resource('filieres', \App\Http\Controllers\Admin\FiliereController::class);

    // Admin Grades Management
    Route::get('/grades', [\App\Http\Controllers\Admin\GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/edit', [\App\Http\Controllers\Admin\GradeController::class, 'edit'])->name('grades.edit');
    Route::post('/grades/store', [\App\Http\Controllers\Admin\GradeController::class, 'store'])->name('grades.store');

    // Dynamic API for cascade selects (Filière → Groupe → Module)
    Route::get('/api/filieres/{filiere}/groups', [\App\Http\Controllers\Admin\ApiController::class, 'getGroups'])->name('api.filiere.groups');
    Route::get('/api/groups/{group}/modules', [\App\Http\Controllers\Admin\ApiController::class, 'getModules'])->name('api.group.modules');
    Route::get('/api/rooms/{room}/availability', [\App\Http\Controllers\Admin\ApiController::class, 'getRoomAvailability'])->name('api.room.availability');

    // Internal API test suite protected for admins
    Route::get('/test-api-suite', function () {
        return view('test_api');
    })->name('test-api-suite');

    // Admin Reservations Management
    Route::post('/reservations/{reservation}/approve', [\App\Http\Controllers\Admin\ReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{reservation}/reject', [\App\Http\Controllers\Admin\ReservationController::class, 'reject'])->name('reservations.reject');
    Route::resource('reservations', \App\Http\Controllers\Admin\ReservationController::class);

    Route::post('/requests/{request}/update-status-ajax', [\App\Http\Controllers\RequestController::class, 'updateStatusAjax'])->name('requests.update-status-ajax');
    Route::resource('requests', \App\Http\Controllers\RequestController::class)->only(['index', 'update']);
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'query'])->name('search');
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}/reply', [\App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/reply', [\App\Http\Controllers\Admin\MessageController::class, 'sendReply'])->name('messages.send-reply');
    Route::delete('/messages/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('messages.destroy');

    // Export CSV Routes
    Route::get('/export/students', [\App\Http\Controllers\Admin\ExportController::class, 'students'])->name('export.students');
    Route::get('/export/grades', [\App\Http\Controllers\Admin\ExportController::class, 'grades'])->name('export.grades');
    Route::get('/export/absences', [\App\Http\Controllers\Admin\ExportController::class, 'absences'])->name('export.absences');

    // Activity Log Route
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Cahier de Textes global consult
    Route::get('/textbooks', [\App\Http\Controllers\TextbookController::class, 'adminIndex'])->name('textbooks.index');

    // Validation des Absences
    Route::get('/absences', [\App\Http\Controllers\AbsenceController::class, 'adminIndex'])->name('absences.index');
    Route::post('/absences/{absence}/approve', [\App\Http\Controllers\AbsenceController::class, 'approveJustification'])->name('absences.approve');
    Route::post('/absences/{absence}/reject', [\App\Http\Controllers\AbsenceController::class, 'rejectJustification'])->name('absences.reject');
    Route::post('/absences/{absence}/force-justify', [\App\Http\Controllers\AbsenceController::class, 'forceJustify'])->name('absences.force-justify');
    Route::delete('/absences/{absence}', [\App\Http\Controllers\AbsenceController::class, 'destroy'])->name('absences.destroy');
});

Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Professor\ProfessorController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [\App\Http\Controllers\ScheduleController::class, 'professorSchedule'])->name('schedule');
    Route::get('/grades', [\App\Http\Controllers\GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/edit/{group}/{module}', [\App\Http\Controllers\GradeController::class, 'editGroup'])->name('grades.edit');
    Route::post('/grades/store', [\App\Http\Controllers\GradeController::class, 'store'])->name('grades.store');
    Route::get('/absences', [\App\Http\Controllers\AbsenceController::class, 'index'])->name('absences.index');
    Route::get('/absences/create/{schedule}', [\App\Http\Controllers\AbsenceController::class, 'createForm'])->name('absences.create');
    Route::post('/absences/store', [\App\Http\Controllers\AbsenceController::class, 'store'])->name('absences.store');

    // Reservations
    Route::get('/reservations', [\App\Http\Controllers\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [\App\Http\Controllers\ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'store'])->name('reservations.store');



    // Cahier de Textes
    Route::get('/textbook', [\App\Http\Controllers\TextbookController::class, 'index'])->name('textbook.index');
    Route::get('/textbook/create', [\App\Http\Controllers\TextbookController::class, 'create'])->name('textbook.create');
    Route::post('/textbook', [\App\Http\Controllers\TextbookController::class, 'store'])->name('textbook.store');

    // Demandes Administratives Professeur
    Route::get('/requests/create', [\App\Http\Controllers\RequestController::class, 'createProfessorRequest'])->name('requests.create');
    Route::post('/requests', [\App\Http\Controllers\RequestController::class, 'store'])->name('requests.store');

    // Disponibilités pour les examens
    Route::get('/availability', [\App\Http\Controllers\Professor\AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [\App\Http\Controllers\Professor\AvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{availability}', [\App\Http\Controllers\Professor\AvailabilityController::class, 'destroy'])->name('availability.destroy');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Student\StudentController::class, 'index'])->name('dashboard');
    Route::get('/grades', [\App\Http\Controllers\Student\StudentController::class, 'grades'])->name('grades');
    Route::get('/absences', [\App\Http\Controllers\Student\StudentController::class, 'absences'])->name('absences');
    Route::get('/schedule', [\App\Http\Controllers\ScheduleController::class, 'studentSchedule'])->name('schedule');
    Route::post('/absences/{absence}/justify', [\App\Http\Controllers\AbsenceController::class, 'uploadJustification'])->name('absences.justify');
    Route::get('/requests', [\App\Http\Controllers\Student\StudentController::class, 'createRequest'])->name('requests.index');
    Route::get('/requests/create', [\App\Http\Controllers\Student\StudentController::class, 'createRequest'])->name('requests.create');
    Route::post('/requests', [\App\Http\Controllers\RequestController::class, 'store'])->name('requests.store');

    // Convocations
    Route::get('/convocations', [\App\Http\Controllers\Student\ConvocationController::class, 'index'])->name('convocations.index');
    Route::get('/convocations/{convocation}/download', [\App\Http\Controllers\Student\ConvocationController::class, 'download'])->name('convocations.download');
});

// Shared Classroom
Route::middleware(['auth'])->group(function () {
    Route::get('/classroom', [\App\Http\Controllers\ClassroomController::class, 'index'])->name('classroom.index');
    Route::get('/classroom/show/{group}/{module}', [\App\Http\Controllers\ClassroomController::class, 'showClassroom'])->name('classroom.show');
    Route::post('/classroom/post/{group}/{module}', [\App\Http\Controllers\ClassroomController::class, 'storePost'])->name('classroom.post');
    Route::post('/classroom/comment/{post}', [\App\Http\Controllers\ClassroomController::class, 'storeComment'])->name('classroom.comment');
    Route::get('/classroom/file/{post}', [\App\Http\Controllers\ClassroomController::class, 'downloadFile'])->name('classroom.download_file');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/calendar', [\App\Http\Controllers\ScheduleController::class, 'calendar'])->name('calendar');
    Route::get('/schedules/export/pdf', [\App\Http\Controllers\ScheduleController::class, 'exportPdf'])->name('schedules.pdf');
    Route::get('/faq', function () {
        return view('faq');
    })->name('faq');
    Route::get('/admin/requests/show/{adminRequest}', [\App\Http\Controllers\RequestController::class, 'show'])->name('admin.requests.show');

    // Notifications
    Route::post('/notifications/{id}/read', function ($id) {
        $notif = Auth::user()->notifications()->find($id);
        if ($notif) $notif->markAsRead();
        return response()->json(['ok' => true]);
    })->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');

    // Chat / Messaging
    Route::get('/chat', [\App\Http\Controllers\MessageController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [\App\Http\Controllers\MessageController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}', [\App\Http\Controllers\MessageController::class, 'store'])->name('chat.store');
    Route::post('/chat/start/{user}', [\App\Http\Controllers\MessageController::class, 'startConversation'])->name('chat.start');
    
    // Secure route for downloading absence justifications
    Route::get('/absences/{absence}/justification', [\App\Http\Controllers\AbsenceController::class, 'downloadJustification'])->name('absences.justification');
});


Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

require __DIR__ . '/auth.php';
