<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentImportController extends Controller
{
    public function show()
    {
        $groups = Group::all();
        return view('admin.students.import', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'students' => 'required|array|min:1',
            'students.*.student_number' => 'required|string|distinct',
            'students.*.name' => 'required|string|max:255',
            'students.*.email' => 'required|email|distinct',
        ]);

        $groupId = $request->group_id;
        $studentRole = Role::where('name', 'student')->first();
        if (!$studentRole) {
            return response()->json(['error' => "Le rôle 'étudiant' n'est pas configuré dans le système."], 500);
        }

        $currentYear = AcademicYear::where('is_current', true)->first();
        $academicYearId = $currentYear ? $currentYear->id : null;

        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->students as $i => $item) {
                // Double check constraints on backend
                if (User::where('email', $item['email'])->exists()) {
                    $errors[] = "Ligne " . ($i + 1) . " : L'email '{$item['email']}' est déjà utilisé.";
                    continue;
                }

                if (Student::where('student_number', $item['student_number'])->exists()) {
                    $errors[] = "Ligne " . ($i + 1) . " : Le numéro d'étudiant '{$item['student_number']}' existe déjà.";
                    continue;
                }

                // 1. Create User
                $user = User::create([
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => Hash::make($item['student_number']), // Default password is the student number
                    'role_id' => $studentRole->id,
                ]);

                // 2. Create Student profile
                Student::create([
                    'user_id' => $user->id,
                    'group_id' => $groupId,
                    'student_number' => $item['student_number'],
                    'academic_year_id' => $academicYearId,
                ]);

                $successCount++;
            }

            if (count($errors) > 0 && $successCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "L'importation a échoué en raison d'erreurs de contrainte.",
                    'errors' => $errors,
                ], 422);
            }

            DB::commit();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'STUDENT_BULK_IMPORT',
                'description' => "Importation en masse réussie de {$successCount} étudiants dans le groupe ID: {$groupId}.",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Importation réussie de {$successCount} étudiants.",
                'success_count' => $successCount,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Une erreur imprévue est survenue lors de l'importation.",
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}
