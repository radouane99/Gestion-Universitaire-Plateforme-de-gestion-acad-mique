<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Absence;
use Illuminate\Support\Facades\Auth;

class AcademicApiController extends Controller
{
    /**
     * Login for API token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        
        // Note: Prior tokens are kept to allow multiple devices (as requested in the audit).
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? 'user',
            ]
        ], 200);
    }

    /**
     * Get all modules.
     */
    public function modules()
    {
        $perPage = (int) request()->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));
        return response()->json([
            'status' => 'success',
            'modules' => Module::paginate($perPage)
        ], 200);
    }

    /**
     * Get grades for the authenticated student.
     */
    public function grades()
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only students can view their grades.'
            ], 403);
        }

        if (!$user->student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student profile not found for this user.'
            ], 404);
        }

        $perPage = (int) request()->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $grades = Grade::where('student_id', $user->student->id)
            ->with('module')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'student' => [
                'id' => $user->student->id,
                'student_number' => $user->student->student_number,
                'name' => $user->name,
            ],
            'grades' => $grades
        ], 200);
    }

    /**
     * Get schedule for the authenticated user.
     */
    public function schedule()
    {
        $user = Auth::user();
        $query = Schedule::with(['module', 'room', 'professor.user', 'group']);

        if ($user->isStudent()) {
            if (!$user->student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student profile not found.'
                ], 404);
            }
            $query->where('group_id', $user->student->group_id);
        } elseif ($user->isProfessor()) {
            if (!$user->professor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Professor profile not found.'
                ], 404);
            }
            $query->where('professor_id', $user->professor->id);
        } else {
            if (!$user->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only students, professors, and admins can query schedules.'
                ], 403);
            }
        }

        $perPage = (int) request()->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));
        return response()->json([
            'status' => 'success',
            'role' => $user->role->name ?? 'unknown',
            'schedules' => $query->paginate($perPage)
        ], 200);
    }

    /**
     * Get absences for the authenticated student.
     */
    public function absences()
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only students can view their absences.'
            ], 403);
        }

        if (!$user->student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student profile not found.'
            ], 404);
        }

        $perPage = (int) request()->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $absences = Absence::where('student_id', $user->student->id)
            ->with('module')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'student' => [
                'id' => $user->student->id,
                'student_number' => $user->student->student_number,
                'name' => $user->name,
            ],
            'absences' => $absences
        ], 200);
    }

    /**
     * Get exams for the authenticated user.
     */
    public function exams()
    {
        $user = Auth::user();
        $query = \App\Models\Exam::with(['module', 'room', 'group', 'proctors.user']);

        if ($user->isStudent()) {
            if (!$user->student) {
                return response()->json(['status' => 'error', 'message' => 'Student profile not found.'], 404);
            }
            $query->where('group_id', $user->student->group_id);
        } elseif ($user->isProfessor()) {
            if (!$user->professor) {
                return response()->json(['status' => 'error', 'message' => 'Professor profile not found.'], 404);
            }
            $query->whereHas('proctors', function($q) use ($user) {
                $q->where('professor_id', $user->professor->id);
            });
        } elseif (!$user->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        // Return all exams for the calendar, no pagination needed as exams are limited per semester
        $exams = $query->get();

        return response()->json([
            'status' => 'success',
            'role' => $user->role->name ?? 'unknown',
            'exams' => $exams->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'date' => $exam->date,
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                    'module' => $exam->module,
                    'room' => $exam->room,
                    'group' => $exam->group,
                    'proctors' => $exam->proctors,
                    'title' => 'EXAMEN: ' . ($exam->module->name ?? 'Matière'),
                ];
            })
        ], 200);
    }

    /**
     * Get appointments for the authenticated user.
     */
    public function appointments()
    {
        $user = Auth::user();
        
        $query = \App\Models\Appointment::with(['slot.host', 'student.user']);

        if ($user->isStudent()) {
            if (!$user->student) {
                return response()->json(['status' => 'error', 'message' => 'Student profile not found.'], 404);
            }
            $query->where('student_id', $user->student->id);
        } elseif ($user->isProfessor()) {
            if (!$user->professor) {
                return response()->json(['status' => 'error', 'message' => 'Professor profile not found.'], 404);
            }
            $query->whereHas('slot', function ($q) use ($user) {
                $q->where('host_id', $user->id);
            });
        } elseif (!$user->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        } else {
            // Admins can see appointments where they are the host
            $query->whereHas('slot', function ($q) use ($user) {
                $q->where('host_id', $user->id);
            });
        }

        // Return all active/pending appointments for the calendar
        $appointments = $query->whereIn('status', ['scheduled', 'requested', 'suggested'])->get();

        return response()->json([
            'status' => 'success',
            'role' => $user->role->name ?? 'unknown',
            'appointments' => $appointments->map(function ($appt) {
                return [
                    'id' => $appt->id,
                    'status' => $appt->status,
                    'purpose' => $appt->purpose,
                    'start_time' => $appt->slot?->start_time?->toIso8601String() ?? ($appt->slot?->start_time ?? ''),
                    'end_time' => $appt->slot?->end_time?->toIso8601String() ?? ($appt->slot?->end_time ?? ''),
                    'host_name' => $appt->slot?->host?->name ?? 'Intervenant',
                    'student_name' => $appt->student?->user?->name ?? 'Étudiant',
                    'group_name' => $appt->student?->group?->name ?? 'Sans groupe',
                ];
            })
        ], 200);
    }
}

