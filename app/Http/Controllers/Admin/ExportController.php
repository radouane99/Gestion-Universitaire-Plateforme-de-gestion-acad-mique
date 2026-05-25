<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Absence;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function students(): StreamedResponse
    {
        $filename = 'etudiants_upf_' . date('Y-m-d') . '.csv';

        \App\Models\ActivityLog::log('exported', 'CSV', "Export de la liste des étudiants en CSV.");

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Header
            fputcsv($handle, ['N° Étudiant', 'Nom Complet', 'Email', 'Groupe', 'Date Inscription'], ';');

            $students = Student::with(['user', 'group'])->get();
            foreach ($students as $student) {
                fputcsv($handle, [
                    'STU-' . $student->id,
                    $student->user->name ?? 'N/A',
                    $student->user->email ?? 'N/A',
                    $student->group->name ?? 'N/A',
                    $student->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function grades(): StreamedResponse
    {
        $filename = 'notes_upf_' . date('Y-m-d') . '.csv';

        \App\Models\ActivityLog::log('exported', 'CSV', "Export de la liste des notes en CSV.");

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['Étudiant', 'Module', 'Note Exam', 'Note TP', 'Note Finale', 'Résultat'], ';');

            $grades = Grade::with(['student.user', 'module'])->get();
            foreach ($grades as $grade) {
                fputcsv($handle, [
                    $grade->student->user->name ?? 'N/A',
                    $grade->module->name ?? 'N/A',
                    $grade->exam ?? 'N/A',
                    $grade->tp ?? 'N/A',
                    $grade->final_grade ?? 'N/A',
                    ($grade->final_grade >= 10) ? 'Validé' : 'Non Validé',
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function absences(): StreamedResponse
    {
        $filename = 'absences_upf_' . date('Y-m-d') . '.csv';

        \App\Models\ActivityLog::log('exported', 'CSV', "Export de la liste des absences en CSV.");

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['Étudiant', 'Date', 'Justifiée', 'Motif'], ';');

            $absences = Absence::with(['student.user'])->get();
            foreach ($absences as $absence) {
                fputcsv($handle, [
                    $absence->student->user->name ?? 'N/A',
                    $absence->date ?? 'N/A',
                    $absence->is_justified ? 'Oui' : 'Non',
                    $absence->reason ?? '-',
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
