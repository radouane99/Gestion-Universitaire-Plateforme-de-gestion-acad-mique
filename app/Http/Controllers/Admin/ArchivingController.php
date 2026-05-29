<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Absence;
use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArchivingController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $settings = Setting::first();
        return view('admin.archiving.index', compact('currentYear', 'settings'));
    }

    public function rollover(Request $request)
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $settings = Setting::first();
        
        $expectedConfirmation = "ARCHIVER " . ($settings?->academic_year ?? '2025-2026');

        $request->validate([
            'confirmation' => 'required|string',
            'next_year_name' => 'required|string|max:50',
        ]);

        if (trim($request->confirmation) !== $expectedConfirmation) {
            return back()->with('error', "La phrase de confirmation est incorrecte. Veuillez saisir exactement : '{$expectedConfirmation}'");
        }

        if (!$currentYear) {
            return back()->with('error', "Aucune année académique active n'a été trouvée pour exécuter le basculement.");
        }

        DB::beginTransaction();
        try {
            // 1. Archive active grades
            Grade::where('is_archived', false)
                ->update([
                    'is_archived' => true,
                    'academic_year_id' => $currentYear->id,
                ]);

            // 2. Archive active absences
            Absence::where('is_archived', false)
                ->update([
                    'is_archived' => true,
                    'academic_year_id' => $currentYear->id,
                ]);

            // 3. Archive active exams
            Exam::where('is_archived', false)
                ->update([
                    'is_archived' => true,
                    'academic_year_id' => $currentYear->id,
                ]);

            // 4. Mark old year as not current
            $currentYear->is_current = false;
            $currentYear->save();

            // 5. Create new academic year
            $newYear = AcademicYear::create([
                'name' => $request->next_year_name,
                'is_current' => true,
            ]);

            // 6. Move students to new academic year enrollments
            Student::query()->update(['academic_year_id' => $newYear->id]);

            // 7. Update Institution Settings
            if ($settings) {
                $settings->academic_year = $request->next_year_name;
                $settings->save();
            }

            // 8. Log the critical operation
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'ANNUAL_ARCHIVING_ROLLOVER',
                'description' => "Clôture annuelle et archivage de l'année {$currentYear->name}. Ouverture de la nouvelle année académique {$newYear->name}.",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('admin.archiving.index')
                ->with('success', "L'archivage annuel et le basculement vers l'année {$newYear->name} ont été effectués avec succès. 🚀");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Une erreur est survenue durant le basculement : " . $e->getMessage());
        }
    }
}
