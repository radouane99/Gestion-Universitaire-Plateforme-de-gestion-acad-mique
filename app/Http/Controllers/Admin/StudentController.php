<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Group;
use App\Models\ActivityLog;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $role = Role::where('name', 'student')->firstOrFail();
        $query = User::with(['student.group.filiere'])->where('role_id', $role->id);

        if ($request->filled('filiere_id')) {
            $query->whereHas('student.group', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        if ($request->filled('group_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        $students = $query->paginate(15)->withQueryString();
        $filieres = \App\Models\Filiere::all();
        $groups = \App\Models\Group::all();
        
        return view('admin.students.index', compact('students', 'filieres', 'groups'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.students.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'group_id' => 'required|exists:groups,id',
            'student_number' => 'required|unique:students,student_number',
        ]);

        $role = Role::firstOrCreate(['name' => 'student']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
        ]);

        Student::create([
            'user_id' => $user->id,
            'group_id' => $validated['group_id'],
            'student_number' => $validated['student_number'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Student',
            'description' => "Création manuelle de l'étudiant '{$user->name}'.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Étudiant créé avec succès.');
    }

    public function show(User $student)
    {
        // Check if actually a student
        if (!$student->isStudent()) abort(404);
        $student->load(['student.group']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(User $student)
    {
        if (!$student->isStudent()) abort(404);
        $student->load('student');
        $groups = Group::all();
        return view('admin.students.edit', compact('student', 'groups'));
    }

    public function update(Request $request, User $student)
    {
        if (!$student->isStudent()) abort(404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
            'group_id' => 'required|exists:groups,id',
            'student_number' => 'required|unique:students,student_number,' . ($student->student ? $student->student->id : 'NULL'),
        ]);

        $student->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $student->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $student->student()->updateOrCreate(
            ['user_id' => $student->id],
            [
                'group_id' => $validated['group_id'],
                'student_number' => $validated['student_number'],
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Student',
            'description' => "Mise à jour de l'étudiant '{$student->name}'.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Étudiant mis à jour avec succès.');
    }

    public function destroy(User $student)
    {
        if (!$student->isStudent()) abort(404);

        $userName = $student->name;
        $student->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Student',
            'description' => "Suppression de l'étudiant '{$userName}'.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Étudiant supprimé avec succès.');
    }

    public function showImportForm()
    {
        return view('admin.students.import');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, ['name', 'email', 'password', 'group_name', 'group_level', 'student_number']);
            fputcsv($file, ['Ahmed El Fassi', 'ahmed.fassi@upf.ac.ma', 'UseAStrongPass!', 'GI-1', 'Licence 1', 'EST202601']);
            fputcsv($file, ['Sofia Mansouri', 'sofia.mansouri@upf.ac.ma', 'UseAStrongPass!', 'IDS-2', 'Licence 2', 'EST202602']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('import_file');
        $role = Role::firstOrCreate(['name' => 'student']);

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) return back()->with('error', 'Le fichier CSV est vide ou invalide.');

        $header = array_map(function($col) { return strtolower(trim($col)); }, $header);

        $importedCount = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            if (count($row) < count($header)) {
                $errors[] = "Ligne {$rowNum}: colonnes manquantes.";
                continue;
            }

            $data = array_combine($header, array_slice($row, 0, count($header)));
            
            $name = trim($data['name'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = trim($data['password'] ?? '');
            
            if (empty($name) || empty($email)) {
                $errors[] = "Ligne {$rowNum}: Nom et Email requis.";
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ligne {$rowNum}: Email invalide.";
                continue;
            }

            if (empty($password)) {
                $errors[] = "Ligne {$rowNum}: Le mot de passe est requis.";
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Ligne {$rowNum}: Email existe déjà.";
                continue;
            }

            $passHash = Hash::make($password);

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $passHash,
                'role_id' => $role->id,
            ]);

            $groupName = trim($data['group_name'] ?? 'GI-1');
            $groupLevel = trim($data['group_level'] ?? 'Licence');
            $studentNumber = trim($data['student_number'] ?? ('EST' . time() . rand(10, 99)));

            $group = Group::firstOrCreate(
                ['name' => $groupName],
                ['level' => $groupLevel]
            );

            Student::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'student_number' => $studentNumber,
            ]);

            $importedCount++;
        }

        fclose($handle);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Student',
            'description' => "Importation CSV de {$importedCount} étudiant(s).",
            'ip_address' => $request->ip()
        ]);

        if (count($errors) > 0) {
            $msg = "Importation avec avertissements : " . implode(' | ', $errors);
            return redirect()->route('admin.students.index')->with('warning', $msg);
        }

        return redirect()->route('admin.students.index')->with('success', "{$importedCount} étudiants importés !");
    }
}
