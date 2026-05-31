<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // On n'affiche que les admins et professeurs (rôle != student)
        $roleStudent = \App\Models\Role::where('name', 'student')->first();
        $users = \App\Models\User::with(['role', 'professor'])
            ->where('role_id', '!=', $roleStudent->id)
            ->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        $groups = \App\Models\Group::all();
        return view('admin.users.create', compact('roles', 'groups'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            // Student specific
            'group_id' => 'nullable|exists:groups,id',
            'student_number' => 'nullable|unique:students,student_number',
            // Professor specific
            'department' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:permanent,vacataire',
            'contract_end_date' => 'nullable|date',
        ]);

        $role = \App\Models\Role::find($validated['role_id']);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
        ]);

        if ($role->name === 'student') {
            \App\Models\Student::create([
                'user_id' => $user->id,
                'group_id' => $request->group_id,
                'student_number' => $request->student_number,
            ]);
        } elseif ($role->name === 'professor') {
            \App\Models\Professor::create([
                'user_id' => $user->id,
                'department' => $request->department,
                'status' => $request->status ?? 'permanent',
                'contract_end_date' => $request->contract_end_date,
            ]);
        }

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'User',
            'description' => "Création manuelle de l'utilisateur '{$user->name}' ({$role->name}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function destroy(\App\Models\User $user)
    {
        $userName = $user->name;
        $user->delete();

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'User',
            'description' => "Suppression de l'utilisateur '{$userName}'.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Show import form for Excel/CSV users.
     */
    public function showImportForm()
    {
        return view('admin.users.import');
    }

    /**
     * Download Excel/CSV format template.
     */
    public function downloadTemplate($type)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $type . '_import_template.csv"',
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['name', 'email', 'password', 'department']);
            fputcsv($file, ['Prof. Tariq Alaoui', 'tariq@upf.ac.ma', 'UseAStrongPass!', 'Génie Informatique']);
            fputcsv($file, ['Dr. Karima Bennani', 'karima@upf.ac.ma', 'UseAStrongPass!', 'Intelligence Artificielle']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import users from Excel/CSV file.
     */
    public function importUsers(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('import_file');
        // Dans ce controller on n'importe que des professeurs (les admins sont créés manuellement)
        $roleType = 'professor';
        $role = \App\Models\Role::where('name', $roleType)->firstOrFail();

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Get headers
        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            return back()->with('error', 'Le fichier CSV est vide ou invalide.');
        }

        // Standardize headers (trim and lowercase)
        $header = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        $importedCount = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            if (count($row) < count($header)) {
                $errors[] = "Ligne {$rowNum}: colonnes manquantes.";
                continue;
            }

            // Map columns
            $data = array_combine($header, array_slice($row, 0, count($header)));
            
            $name = trim($data['name'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = trim($data['password'] ?? '');
            
            if (empty($name) || empty($email)) {
                $errors[] = "Ligne {$rowNum}: Nom et Email sont requis.";
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ligne {$rowNum}: Email '{$email}' invalide.";
                continue;
            }

            if (empty($password)) {
                $errors[] = "Ligne {$rowNum}: Le mot de passe est requis.";
                continue;
            }

            // Verify email uniqueness
            if (\App\Models\User::where('email', $email)->exists()) {
                $errors[] = "Ligne {$rowNum}: L'email '{$email}' existe déjà.";
                continue;
            }

            // Hash password
            $passHash = Hash::make($password);

            // Create User
            $user = \App\Models\User::create([
                'name' => $name,
                'email' => $email,
                'password' => $passHash,
                'role_id' => $role->id,
            ]);

            // Create Subprofile (Seulement professor ici)
            $department = trim($data['department'] ?? 'Génie Informatique');

            \App\Models\Professor::create([
                'user_id' => $user->id,
                'department' => $department,
            ]);

            $importedCount++;
        }

        fclose($handle);

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'User',
            'description' => "Importation CSV réussie de {$importedCount} {$roleType}(s).",
            'ip_address' => $request->ip()
        ]);

        if (count($errors) > 0) {
            $msg = "Importation de {$importedCount} utilisateurs complétée avec des avertissements : " . implode(' | ', $errors);
            return redirect()->route('admin.users.index')->with('warning', $msg);
        }

        return redirect()->route('admin.users.index')->with('success', "{$importedCount} utilisateurs importés avec succès !");
    }

    public function show(\App\Models\User $user)
    {
        $user->load(['role', 'student.group', 'professor']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(\App\Models\User $user)
    {
        $user->load(['role', 'student', 'professor']);
        $roles = \App\Models\Role::all();
        $groups = \App\Models\Group::all();
        return view('admin.users.edit', compact('user', 'roles', 'groups'));
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            // Student specific
            'group_id' => 'nullable|exists:groups,id',
            'student_number' => 'nullable',
            // Professor specific
            'department' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:permanent,vacataire',
            'contract_end_date' => 'nullable|date',
        ]);

        $role = \App\Models\Role::find($validated['role_id']);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $role->id,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        if ($role->name === 'student') {
            if ($user->professor) {
                $user->professor->delete();
            }
            $user->student()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'group_id' => $request->group_id,
                    'student_number' => $request->student_number,
                ]
            );
        } elseif ($role->name === 'professor') {
            if ($user->student) {
                $user->student->delete();
            }
            $user->professor()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'department' => $request->department,
                    'status' => $request->status ?? 'permanent',
                    'contract_end_date' => $request->status === 'vacataire' ? $request->contract_end_date : null,
                ]
            );
        } else {
            if ($user->student) {
                $user->student->delete();
            }
            if ($user->professor) {
                $user->professor->delete();
            }
        }

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'User',
            'description' => "Mise à jour de l'utilisateur '{$user->name}' (Rôle: {$role->name}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }
}
