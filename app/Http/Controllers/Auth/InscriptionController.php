<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Filiere;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class InscriptionController extends Controller
{
    public function showForm()
    {
        // Only guests should see this form
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (!\App\Models\Setting::isInscriptionOpen()) {
            return redirect()->route('welcome')->with('error', "La campagne d'inscription en ligne est actuellement fermée.");
        }

        $filieres = Filiere::all();
        return view('auth.inscription', compact('filieres'));
    }

    public function register(Request $request)
    {
        if (!\App\Models\Setting::isInscriptionOpen()) {
            return redirect()->route('welcome')->with('error', "La campagne d'inscription en ligne est actuellement fermée.");
        }
        $request->validate([
            // Personal & Account Info
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cin' => ['required', 'string', 'max:20', 'unique:students,cin'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string', 'max:255'],

            // Father's details
            'father_name' => ['required', 'string', 'max:255'],
            'father_cin' => ['required', 'string', 'max:20'],
            'father_occupation' => ['required', 'string', 'max:255'],

            // Mother's details
            'mother_name' => ['required', 'string', 'max:255'],
            'mother_cin' => ['required', 'string', 'max:20'],
            'mother_occupation' => ['required', 'string', 'max:255'],

            // Baccalaureate details
            'bac_filiere' => ['required', 'string', 'max:255'],
            'bac_grade' => ['required', 'numeric', 'between:10,20'],
            'bac_mention' => ['required', 'string', 'in:Passable,Assez Bien,Bien,Très Bien'],
            'bac_year' => ['required', 'integer', 'min:2010', 'max:' . date('Y')],

            // Choice of study filiere
            'filiere_id' => ['required', 'exists:filieres,id'],
        ]);

        return DB::transaction(function () use ($request) {
            $role = Role::firstOrCreate(['name' => 'student']);

            // 1. Create the User Account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
            ]);

            // Generate a unique provisional student number
            $provisionalNumber = 'EST-PROV-' . strtoupper(uniqid());

            // Get current academic year
            $currentYear = DB::table('academic_years')->where('is_current', true)->value('id') 
                ?? DB::table('academic_years')->first()?->id;

            // 2. Create the Student Profile
            Student::create([
                'user_id' => $user->id,
                'group_id' => null, // Group is not assigned yet!
                'student_number' => $provisionalNumber,
                'cin' => $request->cin,
                'birth_date' => $request->birth_date,
                'birth_place' => $request->birth_place,
                'father_name' => $request->father_name,
                'father_cin' => $request->father_cin,
                'father_occupation' => $request->father_occupation,
                'mother_name' => $request->mother_name,
                'mother_cin' => $request->mother_cin,
                'mother_occupation' => $request->mother_occupation,
                'bac_filiere' => $request->bac_filiere,
                'bac_grade' => $request->bac_grade,
                'bac_mention' => $request->bac_mention,
                'bac_year' => $request->bac_year,
                'filiere_id' => $request->filiere_id,
                'academic_year_id' => $currentYear,
                'registration_status' => 'pending', // Pending validation
                'registration_type' => 'new',
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'registered',
                'model_type' => 'Student',
                'description' => "Candidature d'inscription soumise par le bachelier '{$user->name}'.",
                'ip_address' => $request->ip(),
            ]);

            // Auto-login the candidate
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Votre candidature a été soumise avec succès !');
        });
    }
}
