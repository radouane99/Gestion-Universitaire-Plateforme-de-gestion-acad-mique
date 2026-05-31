<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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
})->middleware(['auth', 'verified', 'check.contract'])->name('dashboard');

Route::middleware(['auth', 'role:admin', 'admin.2fa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Importation Excel/CSV pour le Staff
    Route::get('/users/import', [\App\Http\Controllers\Admin\UserController::class, 'showImportForm'])->name('users.import.form');
    Route::post('/users/import', [\App\Http\Controllers\Admin\UserController::class, 'importUsers'])->name('users.import');
    Route::get('/users/import/template/{type}', [\App\Http\Controllers\Admin\UserController::class, 'downloadTemplate'])->name('users.import.template');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Gestion des Étudiants
    Route::get('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'showImportForm'])->name('students.import.form');
    Route::post('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'importStudents'])->name('students.import');
    Route::get('/students/import/template', [\App\Http\Controllers\Admin\StudentController::class, 'downloadTemplate'])->name('students.import.template');
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);

    // Inscription & Réinscription Administrative
    Route::get('/registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('/registrations/{student}/approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/{student}/reject', [\App\Http\Controllers\Admin\RegistrationController::class, 'reject'])->name('registrations.reject');
    Route::post('/registrations/auto-dispatch', [\App\Http\Controllers\Admin\RegistrationController::class, 'autoDispatch'])->name('registrations.auto_dispatch');

    // Gestion des Crédits Modules & Dérogations exceptionnelles
    Route::get('/students-credits', [\App\Http\Controllers\Admin\StudentCreditController::class, 'index'])->name('student_credits.index');
    Route::get('/students-credits/{student}/manage', [\App\Http\Controllers\Admin\StudentCreditController::class, 'manage'])->name('student_credits.manage');
    Route::post('/students-credits/{student}/add', [\App\Http\Controllers\Admin\StudentCreditController::class, 'addCredit'])->name('student_credits.add');
    Route::put('/students-credits/{student}/credits/{module}', [\App\Http\Controllers\Admin\StudentCreditController::class, 'updateCredit'])->name('student_credits.update');
    Route::delete('/students-credits/{student}/credits/{module}', [\App\Http\Controllers\Admin\StudentCreditController::class, 'removeCredit'])->name('student_credits.remove');
    Route::put('/students-credits/{student}/derogation', [\App\Http\Controllers\Admin\StudentCreditController::class, 'updateDerogation'])->name('student_credits.update_derogation');

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
    Route::get('/exams/sessions/{session}/planning', [\App\Http\Controllers\Admin\ExamPlanningController::class, 'simulation'])->name('exams.planning.simulation');
    Route::post('/exams/sessions/{session}/planning/generate', [\App\Http\Controllers\Admin\ExamPlanningController::class, 'generate'])->name('exams.planning.generate');
    Route::post('/exams/sessions/{session}/planning/validate', [\App\Http\Controllers\Admin\ExamPlanningController::class, 'validatePlanning'])->name('exams.planning.validate');
    Route::post('/exams/sessions/{session}/planning/convocations', [\App\Http\Controllers\Admin\ExamPlanningController::class, 'generateConvocations'])->name('exams.planning.convocations');
    Route::post('/exams/sessions/{session}/planning/publish', [\App\Http\Controllers\Admin\ExamPlanningController::class, 'publish'])->name('exams.planning.publish');
    
    // Exam Display Lists
    Route::get('/exams/{exam}/display-list', [\App\Http\Controllers\Admin\ExamDisplayListController::class, 'show'])->name('exams.display_list.show');
    Route::get('/exams/{exam}/display-list/pdf', [\App\Http\Controllers\Admin\ExamDisplayListController::class, 'downloadPdf'])->name('exams.display_list.pdf');

    Route::post('/exams/auto-schedule', [\App\Http\Controllers\Admin\ExamController::class, 'autoSchedule'])->name('exams.auto-schedule');
    Route::post('/exams/{exam}/generate-convocations', [\App\Http\Controllers\Admin\ExamController::class, 'generateConvocations'])->name('exams.generate_convocations');
    Route::post('/exams/{exam}/send-emails', [\App\Http\Controllers\Admin\ExamController::class, 'sendEmails'])->name('exams.send_emails');
    Route::get('/exams/{exam}/pdf', [\App\Http\Controllers\Admin\ExamController::class, 'generatePdf'])->name('exams.pdf');
    Route::get('/exams/{exam}/attendance-sheet', [\App\Http\Controllers\Admin\ExamController::class, 'attendanceSheet'])->name('exams.attendance_sheet');
    Route::get('/exams/{exam}/pv/pdf', [\App\Http\Controllers\Professor\PVExamenController::class, 'exportPdf'])->name('exams.pv.pdf');
    // Calendar view for exams filtered by filière
    Route::get('/exams/calendar', [\App\Http\Controllers\Admin\ExamController::class, 'showCalendar'])->name('exams.calendar');
    // Auto‑generate exams for a filière & session
    Route::post('/exams/auto-generate', [\App\Http\Controllers\Admin\ExamController::class, 'autoGenerate'])->name('exams.auto_generate');
    // API endpoint providing events for FullCalendar
    Route::get('/exams/api/calendar', [\App\Http\Controllers\Admin\ExamController::class, 'calendarData'])->name('exams.api.calendar');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);

    // Admin Convocations (Student + Professor — Session-level management)
    Route::get('/convocations', [\App\Http\Controllers\Admin\ConvocationController::class, 'index'])->name('convocations.index');
    Route::post('/convocations/generate-session', [\App\Http\Controllers\Admin\ConvocationController::class, 'generateForSession'])->name('convocations.generate_session');
    Route::post('/convocations/send-session', [\App\Http\Controllers\Admin\ConvocationController::class, 'sendForSession'])->name('convocations.send_session');
    Route::post('/convocations/auto-assign', [\App\Http\Controllers\Admin\ConvocationController::class, 'autoAssignProctors'])->name('convocations.auto_assign');
    Route::post('/convocations/generate-professors', [\App\Http\Controllers\Admin\ConvocationController::class, 'generateProfessors'])->name('convocations.generate_professors');
    Route::post('/convocations/send-professors', [\App\Http\Controllers\Admin\ConvocationController::class, 'sendProfessors'])->name('convocations.send_professors');
    Route::get('/professor-availabilities', [\App\Http\Controllers\Admin\ConvocationController::class, 'professorAvailabilities'])->name('convocations.professor_availabilities');

    // Convocations Scanner (for proctors/admins)
    Route::get('/convocations/{reference}/verify', [\App\Http\Controllers\Admin\ExamController::class, 'verifyConvocation'])->name('convocations.verify');
    Route::post('/convocations/{reference}/present', [\App\Http\Controllers\Admin\ExamController::class, 'markConvocationPresent'])->name('convocations.mark_present');

    Route::resource('schedules', \App\Http\Controllers\ScheduleController::class);
    Route::resource('filieres', \App\Http\Controllers\Admin\FiliereController::class);

    // Admin Grades Management
    Route::get('/grades', [\App\Http\Controllers\Admin\GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/edit', [\App\Http\Controllers\Admin\GradeController::class, 'edit'])->name('grades.edit');
    Route::post('/grades/store', [\App\Http\Controllers\Admin\GradeController::class, 'store'])->name('grades.store');

    // PV Globaux & Synthèses Académiques
    Route::get('/pv-globaux', [\App\Http\Controllers\Admin\PVGlobalController::class, 'index'])->name('pv_globaux.index');
    Route::get('/pv-globaux/excel', [\App\Http\Controllers\Admin\PVGlobalController::class, 'exportExcel'])->name('pv_globaux.export_excel');
    Route::get('/pv-globaux/pdf', [\App\Http\Controllers\Admin\PVGlobalController::class, 'exportPdf'])->name('pv_globaux.export_pdf');
    Route::post('/pv-globaux/validate', [\App\Http\Controllers\Admin\PVGlobalController::class, 'validatePV'])->name('pv_globaux.validate');
    Route::post('/pv-globaux/reject', [\App\Http\Controllers\Admin\PVGlobalController::class, 'rejectPV'])->name('pv_globaux.reject');

    // Documents Officiels
    Route::get('/students/{student}/releve-notes/{academicYear}', [\App\Http\Controllers\Admin\DocumentController::class, 'releveNotes'])->name('documents.releve');
    Route::get('/students/{student}/attestation-reussite/{academicYear}', [\App\Http\Controllers\Admin\DocumentController::class, 'attestationReussite'])->name('documents.attestation');

    // Assistant IA Admin
    Route::post('/students/{student}/ai-report', [\App\Http\Controllers\Admin\AiAdminController::class, 'generateReport'])->name('students.ai-report');

    // Dynamic API for cascade selects (Filière → Groupe → Module)
    Route::get('/api/filieres/{filiere}/groups', [\App\Http\Controllers\Admin\ApiController::class, 'getGroups'])->name('api.filiere.groups');
    Route::get('/api/groups/{group}/modules', [\App\Http\Controllers\Admin\ApiController::class, 'getModules'])->name('api.group.modules');
    Route::get('/api/rooms/{room}/availability', [\App\Http\Controllers\Admin\ApiController::class, 'getRoomAvailability'])->name('api.room.availability');

    // Internal API test suite protected for admins and only available in local/testing environments
    if (app()->environment(['local', 'testing'])) {
        Route::get('/test-api-suite', function () {
            return view('test_api');
        })->name('test-api-suite');
    }

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
    // Enhanced exports with filters
    Route::get('/export/grades/group/{group}', [\App\Http\Controllers\Admin\ExportController::class, 'gradesByGroup'])->name('export.grades.group');
    Route::get('/export/grades/module/{module}', [\App\Http\Controllers\Admin\ExportController::class, 'gradesByModule'])->name('export.grades.module');
    Route::get('/export/statistics', [\App\Http\Controllers\Admin\ExportController::class, 'statistics'])->name('export.statistics');
    // Evaluations Routes
    Route::get('/evaluations', [\App\Http\Controllers\Admin\EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations/toggle', [\App\Http\Controllers\Admin\EvaluationController::class, 'toggle'])->name('evaluations.toggle');
    // Internships Admin Routes (Option D)
    Route::get('/internships', [\App\Http\Controllers\Admin\InternshipController::class, 'index'])->name('internships.index');
    Route::post('/internships/{internship}/approve', [\App\Http\Controllers\Admin\InternshipController::class, 'approve'])->name('internships.approve');
    Route::post('/internships/{internship}/reject', [\App\Http\Controllers\Admin\InternshipController::class, 'reject'])->name('internships.reject');

    // Activity Log Route
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Paramètres Institution
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/run-migrations', [\App\Http\Controllers\Admin\SettingController::class, 'runMigrations'])->name('settings.run_migrations');

    // Cahier de Textes global consult
    Route::get('/textbooks', [\App\Http\Controllers\TextbookController::class, 'adminIndex'])->name('textbooks.index');

    // Contrôle des Heures (Hours Control)
    Route::get('/hours', [\App\Http\Controllers\ProfessorSessionController::class, 'adminIndex'])->name('hours.index');
    Route::get('/hours/professor/{professor}', [\App\Http\Controllers\ProfessorSessionController::class, 'adminShow'])->name('hours.show');

    // Validation des Absences
    Route::get('/absences', [\App\Http\Controllers\AbsenceController::class, 'adminIndex'])->name('absences.index');
    Route::post('/absences/{absence}/approve', [\App\Http\Controllers\AbsenceController::class, 'approveJustification'])->name('absences.approve');
    Route::post('/absences/{absence}/reject', [\App\Http\Controllers\AbsenceController::class, 'rejectJustification'])->name('absences.reject');
    Route::post('/absences/{absence}/force-justify', [\App\Http\Controllers\AbsenceController::class, 'forceJustify'])->name('absences.force-justify');
    Route::delete('/absences/{absence}', [\App\Http\Controllers\AbsenceController::class, 'destroy'])->name('absences.destroy');

    // ── Pilotage Académique ───────────────────────────────────────────────────
    Route::get('/pilotage', [\App\Http\Controllers\Admin\AcademicPilotingController::class, 'index'])->name('pilotage.index');

    // ── Conseil de Discipline ─────────────────────────────────────────────────
    Route::get('/discipline', [\App\Http\Controllers\Admin\DisciplineCaseController::class, 'index'])->name('discipline.index');
    Route::get('/discipline/{case}', [\App\Http\Controllers\Admin\DisciplineCaseController::class, 'show'])->name('discipline.show');
    Route::post('/discipline/{case}/treat', [\App\Http\Controllers\Admin\DisciplineCaseController::class, 'treat'])->name('discipline.treat');
    Route::post('/discipline/create', [\App\Http\Controllers\Admin\DisciplineCaseController::class, 'create'])->name('discipline.create');

    // ── Présence Examens (Admin) ─────────────────────────────────────────────
    Route::get('/exams/{exam}/attendance', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'index'])->name('exam_attendance.index');
    Route::post('/exams/{exam}/attendance', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'store'])->name('exam_attendance.store');
    Route::post('/exams/{exam}/attendance/{student}/mark', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'markOne'])->name('exam_attendance.mark_one');

    // ── Justifications Examens (Admin) ───────────────────────────────────────
    Route::get('/exam-justifications', [\App\Http\Controllers\Admin\ExamJustificationController::class, 'index'])->name('exam_justifications.index');
    Route::post('/exam-justifications/{justification}/approve', [\App\Http\Controllers\Admin\ExamJustificationController::class, 'approve'])->name('exam_justifications.approve');
    Route::post('/exam-justifications/{justification}/reject', [\App\Http\Controllers\Admin\ExamJustificationController::class, 'reject'])->name('exam_justifications.reject');
    Route::get('/exam-justifications/{justification}/download', [\App\Http\Controllers\Admin\ExamJustificationController::class, 'downloadFile'])->name('exam_justifications.download');

    // ── Rattrapage (Admin) ───────────────────────────────────────────────────
    Route::get('/retake', [\App\Http\Controllers\Admin\RetakeEligibilityController::class, 'index'])->name('retake.index');
    Route::get('/sessions/{session}/retake', [\App\Http\Controllers\Admin\RetakeEligibilityController::class, 'index'])->name('retake.session_index');
    Route::post('/retake/{eligibility}/approve', [\App\Http\Controllers\Admin\RetakeEligibilityController::class, 'approve'])->name('retake.approve');
    Route::post('/retake/{eligibility}/reject', [\App\Http\Controllers\Admin\RetakeEligibilityController::class, 'reject'])->name('retake.reject');
    Route::get('/sessions/{session}/retake/pdf', [\App\Http\Controllers\Admin\RetakeEligibilityController::class, 'exportPdf'])->name('retake.export_pdf');
    Route::get('/sessions/{session}/retake/excel', [\App\Http\Controllers\Admin\RetakeEligibilityController::class, 'exportExcel'])->name('retake.export_excel');

    // Rapports PDF Automatiques
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/absences', [\App\Http\Controllers\Admin\ReportController::class, 'exportAbsences'])->name('reports.absences');
    Route::get('/reports/grades', [\App\Http\Controllers\Admin\ReportController::class, 'exportGrades'])->name('reports.grades');
    Route::get('/reports/exams', [\App\Http\Controllers\Admin\ReportController::class, 'exportExams'])->name('reports.exams');
    Route::get('/reports/rooms', [\App\Http\Controllers\Admin\ReportController::class, 'exportRooms'])->name('reports.rooms');
    Route::get('/reports/at-risk', [\App\Http\Controllers\Admin\ReportController::class, 'exportAtRisk'])->name('reports.at-risk');

    // Étudiants à Risque
    Route::get('/students-risk', [\App\Http\Controllers\Admin\StudentRiskController::class, 'index'])->name('students_risk.index');
    Route::post('/students-risk/{student}/summon', [\App\Http\Controllers\Admin\StudentRiskController::class, 'summon'])->name('students_risk.summon');

    // Réclamations Admin
    Route::get('/reclamations', [\App\Http\Controllers\Admin\ReclamationController::class, 'index'])->name('reclamations.index');

    // Import CSV Premium (overrides old forms for modern preview importer)
    Route::get('/students/import-csv', [\App\Http\Controllers\Admin\StudentImportController::class, 'show'])->name('students.import.show');
    Route::post('/students/import-csv', [\App\Http\Controllers\Admin\StudentImportController::class, 'store'])->name('students.import.store');

    // Archivage Annuel
    Route::get('/archiving', [\App\Http\Controllers\Admin\ArchivingController::class, 'index'])->name('archiving.index');
    Route::post('/archiving/rollover', [\App\Http\Controllers\Admin\ArchivingController::class, 'rollover'])->name('archiving.rollover');

    // Statistiques Avancées
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    // Appointments (Option 12)
    Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'hostIndex'])->name('appointments.index');
    Route::post('/appointments/slots', [\App\Http\Controllers\AppointmentController::class, 'storeSlot'])->name('appointments.slot.store');
    Route::delete('/appointments/slots/{slot}', [\App\Http\Controllers\AppointmentController::class, 'destroySlot'])->name('appointments.slot.destroy');
    Route::post('/appointments/generate-slots', [\App\Http\Controllers\AppointmentController::class, 'generateDefaultSlots'])->name('appointments.generate-slots');

    // Admin 2FA Profile setup
    Route::post('/profile/2fa/init', [\App\Http\Controllers\Auth\AdminTwoFactorController::class, 'initSetup'])->name('2fa.init');
    Route::post('/profile/2fa/confirm', [\App\Http\Controllers\Auth\AdminTwoFactorController::class, 'confirmSetup'])->name('2fa.confirm');
    Route::post('/profile/2fa/disable', [\App\Http\Controllers\Auth\AdminTwoFactorController::class, 'disable'])->name('2fa.disable');
});

Route::middleware(['auth', 'role:professor', 'check.contract'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Professor\ProfessorController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [\App\Http\Controllers\ScheduleController::class, 'professorSchedule'])->name('schedule');
    Route::get('/grades', [\App\Http\Controllers\GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/edit/{group}/{module}', [\App\Http\Controllers\GradeController::class, 'editGroup'])->name('grades.edit');
    Route::post('/grades/store', [\App\Http\Controllers\GradeController::class, 'store'])->name('grades.store');
    Route::get('/absences', [\App\Http\Controllers\AbsenceController::class, 'index'])->name('absences.index');
    Route::get('/absences/create/{schedule}', [\App\Http\Controllers\AbsenceController::class, 'createForm'])->name('absences.create');
    Route::post('/absences/store', [\App\Http\Controllers\AbsenceController::class, 'store'])->name('absences.store');

    // Suivi des Heures (Hours Tracking)
    Route::get('/hours', [\App\Http\Controllers\ProfessorSessionController::class, 'professorIndex'])->name('hours.index');

    // Reservations
    Route::get('/reservations', [\App\Http\Controllers\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [\App\Http\Controllers\ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'store'])->name('reservations.store');

    // Internships Professor Routes (Option D)
    Route::get('/internships', [\App\Http\Controllers\Professor\InternshipController::class, 'index'])->name('internships.index');
    Route::get('/internships/show/{internship}', [\App\Http\Controllers\Professor\InternshipController::class, 'show'])->name('internships.show');
    Route::post('/internships/report/{report}/review', [\App\Http\Controllers\Professor\InternshipController::class, 'reviewReport'])->name('internships.report.review');
    Route::post('/internships/{internship}/grade', [\App\Http\Controllers\Professor\InternshipController::class, 'grade'])->name('internships.grade');
    Route::get('/internships/report/{report}/download', [\App\Http\Controllers\Professor\InternshipController::class, 'downloadReportFile'])->name('internships.report.download');



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

    // Convocations de surveillance professeur
    Route::get('/proctor-convocations', [\App\Http\Controllers\Professor\ProctorConvocationController::class, 'index'])->name('proctor_convocations.index');
    Route::get('/proctor-convocations/{convocation}/download', [\App\Http\Controllers\Professor\ProctorConvocationController::class, 'download'])->name('proctor_convocations.download');
    Route::post('/proctor-convocations/{convocation}/confirm', [\App\Http\Controllers\Professor\ProctorConvocationController::class, 'confirm'])->name('proctor_convocations.confirm');

    // ── Présence Examens (Professeur Surveillant) ────────────────────────────
    Route::get('/exams/{exam}/attendance', [\App\Http\Controllers\Professor\ExamAttendanceController::class, 'index'])->name('exam_attendance.index');
    Route::post('/exams/{exam}/attendance', [\App\Http\Controllers\Professor\ExamAttendanceController::class, 'store'])->name('exam_attendance.store');

    // PV d'Examen
    Route::get('/exams/{exam}/pv/create', [\App\Http\Controllers\Professor\PVExamenController::class, 'create'])->name('exams.pv.create');
    Route::post('/exams/{exam}/pv', [\App\Http\Controllers\Professor\PVExamenController::class, 'store'])->name('exams.pv.store');
    Route::get('/exams/{exam}/pv/pdf', [\App\Http\Controllers\Professor\PVExamenController::class, 'exportPdf'])->name('exams.pv.pdf');

    // Réclamations Professeur
    Route::get('/reclamations', [\App\Http\Controllers\Professor\ReclamationController::class, 'index'])->name('reclamations.index');
    Route::post('/reclamations/{reclamation}/resolve', [\App\Http\Controllers\Professor\ReclamationController::class, 'resolve'])->name('reclamations.resolve');
    
    // Assistant IA (Génération de Brouillon)
    Route::post('/reclamations/{reclamation}/ai-draft', [\App\Http\Controllers\Professor\AiProfessorController::class, 'generateDraft'])->name('ai.draft');

    // Appointments (Option 12)
    Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'hostIndex'])->name('appointments.index');
    Route::post('/appointments/slots', [\App\Http\Controllers\AppointmentController::class, 'storeSlot'])->name('appointments.slot.store');
    Route::delete('/appointments/slots/{slot}', [\App\Http\Controllers\AppointmentController::class, 'destroySlot'])->name('appointments.slot.destroy');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Student\StudentController::class, 'index'])->name('dashboard');

    // Attestation de Réussite
    Route::get('/attestation/download', [\App\Http\Controllers\Student\StudentController::class, 'downloadAttestation'])->name('attestation.download');
    Route::get('/diplome/download', [\App\Http\Controllers\Student\StudentController::class, 'downloadDiplome'])->name('diplome.download');
    Route::get('/receipt/download', [\App\Http\Controllers\Student\StudentController::class, 'downloadReceipt'])->name('receipt.download');

    // Réinscriptions
    Route::get('/reinscription', [\App\Http\Controllers\Student\StudentController::class, 'showReinscriptionForm'])->name('reinscription.form');
    Route::post('/reinscription', [\App\Http\Controllers\Student\StudentController::class, 'processReinscription'])->name('reinscription.store');
    Route::get('/grades', [\App\Http\Controllers\Student\StudentController::class, 'grades'])->name('grades');
    Route::get('/absences', [\App\Http\Controllers\Student\StudentController::class, 'absences'])->name('absences');
    Route::get('/schedule', [\App\Http\Controllers\ScheduleController::class, 'studentSchedule'])->name('schedule');
    Route::post('/absences/{absence}/justify', [\App\Http\Controllers\AbsenceController::class, 'uploadJustification'])->name('absences.justify');
    Route::get('/requests', [\App\Http\Controllers\Student\StudentController::class, 'createRequest'])->name('requests.index');
    Route::get('/requests/create', [\App\Http\Controllers\Student\StudentController::class, 'createRequest'])->name('requests.create');
    Route::post('/requests', [\App\Http\Controllers\RequestController::class, 'store'])->name('requests.store');

    // 🗳️ Anonymous Evaluations (Student)
    Route::get('/evaluations', [\App\Http\Controllers\Student\EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations', [\App\Http\Controllers\Student\EvaluationController::class, 'store'])->name('evaluations.store');

    // Internships Student Routes (Option D)
    Route::get('/internships', [\App\Http\Controllers\Student\InternshipController::class, 'index'])->name('internships.index');
    Route::post('/internships', [\App\Http\Controllers\Student\InternshipController::class, 'store'])->name('internships.store');
    Route::post('/internships/{internship}/report', [\App\Http\Controllers\Student\InternshipController::class, 'storeReport'])->name('internships.report.store');
    Route::get('/internships/report/{report}/download', [\App\Http\Controllers\Student\InternshipController::class, 'downloadReportFile'])->name('internships.report.download');

    // Convocations
    Route::get('/convocations', [\App\Http\Controllers\Student\ConvocationController::class, 'index'])->name('convocations.index');
    Route::get('/convocations/{convocation}/download', [\App\Http\Controllers\Student\ConvocationController::class, 'download'])->name('convocations.download');

    // ── Mes Examens + Rattrapage (Étudiant) ──────────────────────────────────
    Route::get('/exams', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
    Route::get('/retake', [\App\Http\Controllers\Student\ExamController::class, 'showRetake'])->name('retake.index');
    Route::get('/exam-justification/{attendance}/create', [\App\Http\Controllers\Student\ExamJustificationController::class, 'create'])->name('exam_justification.create');
    Route::post('/exam-justification/{attendance}', [\App\Http\Controllers\Student\ExamJustificationController::class, 'store'])->name('exam_justification.store');

    // Réclamations Étudiant
    Route::get('/reclamations', [\App\Http\Controllers\Student\ReclamationController::class, 'index'])->name('reclamations.index');
    Route::get('/reclamations/create', [\App\Http\Controllers\Student\ReclamationController::class, 'create'])->name('reclamations.create');
    Route::post('/reclamations', [\App\Http\Controllers\Student\ReclamationController::class, 'store'])->name('reclamations.store');


    // Appointments (Option 12)
    Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'studentIndex'])->name('appointments.index');
    Route::post('/appointments/book/{slot}', [\App\Http\Controllers\AppointmentController::class, 'book'])->name('appointments.book');
    Route::post('/appointments/request-direct', [\App\Http\Controllers\AppointmentController::class, 'requestDirect'])->name('appointments.request-direct');
    Route::post('/appointments/{appointment}/confirm-suggestion', [\App\Http\Controllers\AppointmentController::class, 'confirmSuggestion'])->name('appointments.confirm-suggestion');
});

// Shared Classroom
Route::middleware(['auth'])->group(function () {
    Route::get('/classroom', [\App\Http\Controllers\ClassroomController::class, 'index'])->name('classroom.index');
    Route::get('/classroom/show/{group}/{module}', [\App\Http\Controllers\ClassroomController::class, 'showClassroom'])->name('classroom.show');
    Route::post('/classroom/post/{group}/{module}', [\App\Http\Controllers\ClassroomController::class, 'storePost'])->name('classroom.post');
    Route::post('/classroom/comment/{post}', [\App\Http\Controllers\ClassroomController::class, 'storeComment'])->name('classroom.comment');
    Route::get('/classroom/file/{post}', [\App\Http\Controllers\ClassroomController::class, 'downloadFile'])->name('classroom.download_file');

    // Devoirs & Soumissions (Option A)
    Route::post('/classroom/homework/{group}/{module}', [\App\Http\Controllers\ClassroomController::class, 'storeHomework'])->name('classroom.homework.store');
    Route::post('/classroom/homework/{homework}/submit', [\App\Http\Controllers\ClassroomController::class, 'storeSubmission'])->name('classroom.submission.store');
    Route::post('/classroom/submission/{submission}/grade', [\App\Http\Controllers\ClassroomController::class, 'gradeSubmission'])->name('classroom.submission.grade');
    Route::get('/classroom/homework/{homework}/download', [\App\Http\Controllers\ClassroomController::class, 'downloadHomeworkFile'])->name('classroom.homework.download');
    Route::get('/classroom/submission/{submission}/download', [\App\Http\Controllers\ClassroomController::class, 'downloadSubmissionFile'])->name('classroom.submission.download');

    // Chat de groupe réactif (Option B)
    Route::get('/classroom/chat/{group}/{module}/messages', [\App\Http\Controllers\ClassroomController::class, 'getMessages'])->name('classroom.chat.messages');
    Route::post('/classroom/chat/{group}/{module}/post', [\App\Http\Controllers\ClassroomController::class, 'postMessage'])->name('classroom.chat.post');
    Route::get('/classroom/chat/download/{message}', [\App\Http\Controllers\ClassroomController::class, 'downloadChatFile'])->name('classroom.chat.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/login/2fa', [\App\Http\Controllers\Auth\AdminTwoFactorController::class, 'showChallenge'])->name('admin.2fa.challenge');
    Route::post('/login/2fa', [\App\Http\Controllers\Auth\AdminTwoFactorController::class, 'verifyChallenge'])->name('admin.2fa.verify');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/calendar', [\App\Http\Controllers\ScheduleController::class, 'calendar'])->name('calendar');
    Route::get('/schedules/export/pdf', [\App\Http\Controllers\ScheduleController::class, 'exportPdf'])->name('schedules.pdf');
    Route::get('/faq', function () {
        return view('faq');
    })->name('faq');
    Route::get('/admin/requests/show/{adminRequest}', [\App\Http\Controllers\RequestController::class, 'show'])->name('admin.requests.show');

    // Appointments cancel (Option 12)
    Route::post('/appointments/{appointment}/cancel', [\App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/accept-request', [\App\Http\Controllers\AppointmentController::class, 'acceptRequest'])->name('appointments.accept-request');
    Route::post('/appointments/{appointment}/suggest-alternative', [\App\Http\Controllers\AppointmentController::class, 'suggestAlternative'])->name('appointments.suggest-alternative');

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
    Route::get('/api/notifications/unread-count', [\App\Http\Controllers\NotificationApiController::class, 'unreadCount'])->name('api.notifications.unread_count');
    Route::get('/api/notifications/latest', [\App\Http\Controllers\NotificationApiController::class, 'latest'])->name('api.notifications.latest');

    // Assistant IA (Smart UPF) - Shared across all roles
    Route::post('/ai/chat', [\App\Http\Controllers\Student\AiChatController::class, 'chat'])->name('ai.chat');

    // Chat / Messaging
    Route::get('/chat', [\App\Http\Controllers\MessageController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [\App\Http\Controllers\MessageController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}', [\App\Http\Controllers\MessageController::class, 'store'])->name('chat.store');
    Route::post('/chat/start/{user}', [\App\Http\Controllers\MessageController::class, 'startConversation'])->name('chat.start');
    
    // Secure route for downloading absence justifications
    Route::get('/absences/{absence}/justification', [\App\Http\Controllers\AbsenceController::class, 'downloadJustification'])->name('absences.justification');

    // Global Search (for all roles)
    Route::get('/global-search', [\App\Http\Controllers\SearchController::class, 'search'])->name('global_search');
});


// Public QR verification for professor surveillance convocations (no sensitive data exposed)
Route::get('/verify/proctor/{reference}', function ($reference) {
    $conv = \App\Models\ProfessorConvocation::with([
        'professor.user',
        'exam.module',
        'exam.room',
        'exam.group',
    ])->where('reference', $reference)->firstOrFail();

    return view('convocations.verify_proctor', compact('conv'));
})->name('convocations.verify_proctor');

// Official Documents Download & Verify
Route::get('/documents/{documentRequest}/download', [\App\Http\Controllers\DocumentController::class, 'downloadPdf'])->name('documents.download')->middleware('auth');
Route::get('/verify/document/{id}/{hash}', [\App\Http\Controllers\DocumentController::class, 'verify'])->name('documents.verify');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// MAGIC SETUP ROUTE FOR RAILWAY
Route::get('/setup-db-magic', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
    return '<h1>🎉 MAGNIFIQUE !</h1><p>La NOUVELLE base de données est migrée avec vos données réelles (250 étudiants, filières, Radouane en Admin) ! Vous pouvez retourner à l\'accueil.</p><a href="/">Retour à l\'accueil</a>';
});

Route::get('/run-migrations', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return '<h1>🚀 MIGRATIONS APPLIQUÉES AVEC SUCCÈS !</h1><p>Les nouvelles tables de dérogations et crédits modules ont été ajoutées sans effacer vos données existantes !</p><a href="/">Retour à l\'accueil</a>';
});

Route::get('/verify-document/{token}', [\App\Http\Controllers\PublicVerificationController::class, 'verifyDocument'])->name('verify.document');

require __DIR__ . '/auth.php';
