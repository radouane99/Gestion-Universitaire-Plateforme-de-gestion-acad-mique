<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Group;
use App\Models\Room;
use App\Models\Professor;
use App\Models\Convocation;
use App\Models\Student;
use App\Mail\ConvocationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::with(['module', 'group', 'room', 'proctors.user'])
            ->withCount([
                'convocations',
                'convocations as sent_convocations_count' => function ($q) {
                    $q->whereNotNull('sent_at');
                }
            ])
            ->when($request->filled('exam_session_id'), function ($q) use ($request) {
                $q->where('exam_session_id', $request->exam_session_id);
            })
            ->when($request->filled('filiere_id'), function ($q) use ($request) {
                $q->whereHas('group', function ($qq) use ($request) {
                    $qq->where('filiere_id', $request->filiere_id);
                });
            })
            ->orderBy('date', 'asc')
            ->get();
            
        $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
        $examSessions = $currentYear ? $currentYear->examSessions : collect();
        $filieres = \App\Models\Filiere::orderBy('name')->get();

        return view('admin.exams.index', compact('exams', 'examSessions', 'filieres'));
    }
    /**
     * Display the exam calendar view.
     */
    public function showCalendar()
    {
        $filieres = \App\Models\Filiere::orderBy('name')->get();
        return view('admin.exams.calendar', compact('filieres'));
    }

    /**
     * Provide JSON data for FullCalendar.
     * Optional filters: filiere_id, group_id, module_id
     */
    public function calendarData(Request $request)
    {
        $query = Exam::with(['module', 'group', 'room'])
            ->when($request->filled('filiere_id'), function ($q) use ($request) {
                $q->whereHas('group.filiere', function ($qq) use ($request) {
                    $qq->where('id', $request->filiere_id);
                });
            })
            ->when($request->filled('group_id'), function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            })
            ->when($request->filled('module_id'), function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            })
            ->get();

        $events = $query->map(function ($exam) {
            return [
                'title' => $exam->module->name . ' - ' . $exam->type,
                'start' => $exam->date . 'T' . $exam->start_time,
                'end' => \Carbon\Carbon::parse($exam->date . ' ' . $exam->start_time)
                    ->addMinutes($exam->duration)
                    ->format('Y-m-d\TH:i:s'),
                'color' => '#'.substr(md5($exam->group->filiere->name), 0, 6), // color per filiere
                'url' => route('admin.exams.edit', $exam),
            ];
        });

        return response()->json($events);
    }

    /**
     * Auto‑generate exams for a session (and optionally a filière).
     */
    public function autoGenerate(Request $request, \App\Services\ExamSchedulerService $scheduler)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'filiere_id' => 'nullable|exists:filieres,id',
            'overwrite' => 'nullable|boolean',
        ]);

        $overwrite = $request->boolean('overwrite', false);
        $result = $scheduler->generateSchedule(
            $request->exam_session_id,
            $request->input('filiere_id'),
            $overwrite
        );

        $redirectParams = [
            'filiere_id' => $request->input('filiere_id'),
            'exam_session_id' => $request->exam_session_id,
        ];

        if ($result['success']) {
            $msg = "Planification automatique terminée. {$result['scheduled']} examens planifiés.";
            if ($result['failed'] > 0) {
                $msg .= " Attention : {$result['failed']} examens n'ont pas pu être planifiés.";
                return redirect()->route('admin.exams.index', $redirectParams)
                    ->with('success', $msg)
                    ->with('error', implode('<br>', $result['errors']));
            }
            return redirect()->route('admin.exams.index', $redirectParams)->with('success', $msg);
        }

        return redirect()->route('admin.exams.index', $redirectParams)
            ->with('error', 'Erreur lors de la planification: ' . $result['message']);
    }

    

    public function autoSchedule(Request $request, \App\Services\ExamSchedulerService $scheduler)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id'
        ]);

        $result = $scheduler->generateSchedule($request->exam_session_id);

        if ($result['success']) {
            $msg = "Planification automatique terminée. {$result['scheduled']} examens planifiés.";
            if ($result['failed'] > 0) {
                $msg .= " Attention : {$result['failed']} examens n'ont pas pu être planifiés.";
                return redirect()->route('admin.exams.index')->with('success', $msg)->with('error', implode('<br>', $result['errors']));
            }
            return redirect()->route('admin.exams.index')->with('success', $msg);
        }

        return redirect()->route('admin.exams.index')->with('error', "Erreur lors de la planification: " . $result['message']);
    }

    public function create()
    {
        $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
        $examSessions = $currentYear ? $currentYear->examSessions : collect();

        $filieres = \App\Models\Filiere::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $professors = Professor::with('user')->get()->sortBy('user.name');
        return view('admin.exams.create', compact('filieres', 'rooms', 'professors', 'examSessions'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'module_id'  => 'required|exists:modules,id',
            'group_id'   => 'required|exists:groups,id',
            'room_id'    => 'required|exists:rooms,id',
            'date'       => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $session = \App\Models\ExamSession::find($request->exam_session_id);
                    if (!$session || !$session->start_date || !$session->end_date) {
                        $fail("La période d'examens pour cette session n'est pas encore définie.");
                    } else {
                        $date = \Carbon\Carbon::parse($value)->startOfDay();
                        if ($date->lt($session->start_date) || $date->gt($session->end_date)) {
                            $fail("La date de l'examen doit être comprise entre le " . $session->start_date->format('d/m/Y') . " et le " . $session->end_date->format('d/m/Y') . ".");
                        }
                    }
                }
            ],
            'start_time' => 'required',
            'duration'   => 'required|integer|min:30',
            'type'       => 'required|in:CC1,CC2,Final',
            'proctors'   => 'nullable|array',
            'proctors.*' => 'exists:professors,id',
        ]);

        $this->addConflictValidation($validator, $request);
        $validated = $validator->validate();

        DB::transaction(function () use ($validated, $request) {
            $exam = Exam::create([
                'exam_session_id' => $validated['exam_session_id'],
                'module_id'  => $validated['module_id'],
                'group_id'   => $validated['group_id'],
                'room_id'    => $validated['room_id'],
                'date'       => $validated['date'],
                'start_time' => $validated['start_time'],
                'duration'   => $validated['duration'],
                'type'       => $validated['type'],
            ]);

            if (!empty($validated['proctors'])) {
                $exam->proctors()->attach($validated['proctors']);
            }

            // Automatically generate convocations for all students of the group
            if ($request->boolean('generate_convocations', true)) {
                $this->generateConvocationsForExam($exam, $request->boolean('send_email', false));
            }
        });

        return redirect()->route('admin.exams.index')->with('success', 'Examen planifié avec succès. Les convocations ont été générées.');
    }

    public function edit(Exam $exam)
    {
        $exam->load('group.filiere');
        
        $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
        $examSessions = $currentYear ? $currentYear->examSessions : collect();

        $filieres = \App\Models\Filiere::orderBy('name')->get();
        // Get groups for the exam's filiere
        $groups = Group::where('filiere_id', $exam->group->filiere_id)->orderBy('name')->get();
        // Get modules for the exam's group using same logic as the API
        $moduleIds = \App\Models\Schedule::where('group_id', $exam->group_id)->pluck('module_id')->unique();
        if ($moduleIds->isEmpty() && $exam->group->filiere_id) {
            $modules = \App\Models\Module::where('filiere_id', $exam->group->filiere_id)->orderBy('name')->get();
        } else {
            $modules = \App\Models\Module::whereIn('id', $moduleIds)->orderBy('name')->get();
        }
        
        $rooms = Room::orderBy('name')->get();
        $professors = Professor::with('user')->get()->sortBy('user.name');
        return view('admin.exams.edit', compact('exam', 'filieres', 'groups', 'modules', 'rooms', 'professors', 'examSessions'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'module_id'  => 'required|exists:modules,id',
            'group_id'   => 'required|exists:groups,id',
            'room_id'    => 'required|exists:rooms,id',
            'date'       => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $session = \App\Models\ExamSession::find($request->exam_session_id);
                    if (!$session || !$session->start_date || !$session->end_date) {
                        $fail("La période d'examens pour cette session n'est pas encore définie.");
                    } else {
                        $date = \Carbon\Carbon::parse($value)->startOfDay();
                        if ($date->lt($session->start_date) || $date->gt($session->end_date)) {
                            $fail("La date de l'examen doit être comprise entre le " . $session->start_date->format('d/m/Y') . " et le " . $session->end_date->format('d/m/Y') . ".");
                        }
                    }
                }
            ],
            'start_time' => 'required',
            'duration'   => 'required|integer|min:30',
            'type'       => 'required|in:CC1,CC2,Final',
            'proctors'   => 'nullable|array',
            'proctors.*' => 'exists:professors,id',
        ]);

        $this->addConflictValidation($validator, $request, $exam->id);
        $validated = $validator->validate();

        DB::transaction(function () use ($validated, $exam) {
            $exam->update([
                'exam_session_id' => $validated['exam_session_id'],
                'module_id'  => $validated['module_id'],
                'group_id'   => $validated['group_id'],
                'room_id'    => $validated['room_id'],
                'date'       => $validated['date'],
                'start_time' => $validated['start_time'],
                'duration'   => $validated['duration'],
                'type'       => $validated['type'],
            ]);

            if (isset($validated['proctors'])) {
                $exam->proctors()->sync($validated['proctors']);
            } else {
                $exam->proctors()->detach();
            }
        });

        return redirect()->route('admin.exams.index')->with('success', 'Examen mis à jour avec succès.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Examen supprimé avec succès.');
    }

    /**
     * Generate convocations for all students of the exam group.
     */
    public function generateConvocations(Request $request, Exam $exam)
    {
        $sendEmail = $request->boolean('send_email', false);
        
        // Count how many already exist
        $alreadyExists = $exam->convocations()->count();
        
        $count = $this->generateConvocationsForExam($exam, $sendEmail);
        
        if ($count === 0 && $alreadyExists > 0) {
            $msg = "Les {$alreadyExists} convocations existent déjà. Utilisez \"Envoyer mails\" pour renvoyer les emails.";
            return back()->with('info', $msg);
        }
        
        $msg = "{$count} convocation(s) générée(s) avec succès.";
        if ($sendEmail) {
            $msg .= " Les emails ont été envoyés.";
        }
        return back()->with('success', $msg);
    }

    /**
     * Send emails to all students with pending convocations for this exam.
     */
    public function sendEmails(Exam $exam)
    {
        $exam->load(['module', 'group', 'room', 'proctors.user']);
        $convocations = $exam->convocations()->with(['student.user', 'student.group.filiere'])->whereNull('sent_at')->get();

        $count = 0;
        foreach ($convocations as $convocation) {
            $this->sendConvocationEmail($convocation);
            $count++;
        }

        return back()->with('success', "{$count} email(s) envoyé(s) avec succès.");
    }

    /**
     * Admin PDF generation (full exam convocation sheet - all students).
     */
    public function generatePdf(Exam $exam)
    {
        $exam->load(['module', 'group.filiere', 'room', 'proctors.user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exams.pdf', compact('exam'));

        return $pdf->download('convocation_examen_' . $exam->module->name . '.pdf');
    }

    /**
     * Generate a professor attendance (emargement) sheet PDF.
     */
    public function attendanceSheet(Exam $exam)
    {
        $exam->load(['module', 'group.filiere', 'room', 'proctors.user']);
        $convocations = $exam->convocations()
            ->with(['student.user'])
            ->orderBy('id')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exams.attendance_sheet', compact('exam', 'convocations'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('emargement_' . $exam->module->name . '_' . $exam->date . '.pdf');
    }

    /**
     * QR Code Scanner View
     */
    public function verifyConvocation($reference)
    {
        $convocation = Convocation::with(['student.user', 'exam.module', 'exam.room'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('convocations.verify', compact('convocation'));
    }

    /**
     * Mark student as present via QR Code
     */
    public function markConvocationPresent($reference)
    {
        $convocation = Convocation::where('reference', $reference)->firstOrFail();
        
        $convocation->update([
            'is_present' => true
        ]);

        return redirect()->route('admin.convocations.verify', $reference)->with('success', 'Présence validée avec succès !');
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function addConflictValidation($validator, Request $request, $ignoreExamId = null)
    {
        $validator->after(function ($validator) use ($request, $ignoreExamId) {
            $date = $request->input('date');
            $startTime = $request->input('start_time');
            $duration = $request->input('duration');
            $roomId = $request->input('room_id');
            $groupId = $request->input('group_id');
            $proctors = $request->input('proctors', []);

            if ($date && $startTime && $duration) {
                $start = \Carbon\Carbon::parse("$date $startTime");
                $end = $start->copy()->addMinutes($duration);

                $conflictQuery = function ($query) use ($startTime, $end, $ignoreExamId) {
                    if ($ignoreExamId) {
                        $query->where('id', '!=', $ignoreExamId);
                    }
                    $query->whereRaw("TIME(start_time) < ?", [$end->format('H:i:s')])
                          ->whereRaw("ADDTIME(TIME(start_time), SEC_TO_TIME(duration * 60)) > ?", [$startTime]);
                };

                // 1. Room Conflict
                if ($roomId) {
                    $roomConflict = Exam::where('room_id', $roomId)
                        ->where('date', $date)
                        ->where($conflictQuery)
                        ->exists();
                    if ($roomConflict) {
                        $validator->errors()->add('room_id', "Cette salle est déjà occupée sur cette plage horaire.");
                    }
                }

                // 2. Group Conflict
                if ($groupId) {
                    $groupConflict = Exam::where('group_id', $groupId)
                        ->where('date', $date)
                        ->where($conflictQuery)
                        ->exists();
                    if ($groupConflict) {
                        $validator->errors()->add('group_id', "Ce groupe a déjà un examen sur cette plage horaire.");
                    }
                }

                // 3. Proctor Conflict
                if (!empty($proctors)) {
                    $proctorConflict = \App\Models\Professor::whereIn('id', $proctors)
                        ->whereHas('exams', function ($query) use ($date, $conflictQuery) {
                            $query->where('date', $date)->where($conflictQuery);
                        })->exists();
                    if ($proctorConflict) {
                        $validator->errors()->add('proctors', "L'un des surveillants sélectionnés surveille déjà un autre examen sur cette plage horaire.");
                    }
                }
            }
        });
    }

    private function generateConvocationsForExam(Exam $exam, bool $sendEmail = false): int
    {
        $exam->load(['module', 'group', 'room', 'proctors.user']);
        $students = Student::where('group_id', $exam->group_id)->with(['user', 'group.filiere'])->get();
        $count = 0;

        foreach ($students as $student) {
            // Skip if already exists
            if (Convocation::where('exam_id', $exam->id)->where('student_id', $student->id)->exists()) {
                continue;
            }

            $convocation = Convocation::create([
                'exam_id'    => $exam->id,
                'student_id' => $student->id,
                'reference'  => Convocation::generateReference(),
                'status'     => 'pending',
            ]);

            if ($sendEmail) {
                $this->sendConvocationEmail($convocation);
            }

            $count++;
        }

        return $count;
    }

    private function sendConvocationEmail(Convocation $convocation): void
    {
        $convocation->load(['exam.module', 'exam.room', 'exam.proctors.user', 'student.user', 'student.group.filiere']);

        // Generate PDF content for attachment
        $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadView('convocations.pdf', compact('convocation'))
            ->output();

        try {
            Mail::to($convocation->student->user->email)
                ->send(new ConvocationMail($convocation, $pdfContent));

            $convocation->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Exception $e) {
            // Log error but don't fail the whole operation
            \Log::error("Failed to send convocation email for {$convocation->reference}: " . $e->getMessage());
        }
    }
}

