<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Absence;
use App\Models\Group;
use App\Models\Module;
use App\Models\Filiere;
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
            
            fputcsv($handle, ['Étudiant', 'Module', 'CC1', 'CC2', 'Exam', 'Note Finale', 'Résultat'], ';');

            $grades = Grade::with(['student.user', 'module'])->get();
            foreach ($grades as $grade) {
                fputcsv($handle, [
                    $grade->student->user->name ?? 'N/A',
                    $grade->module->name ?? 'N/A',
                    $grade->cc1 ?? 'N/A',
                    $grade->cc2 ?? 'N/A',
                    $grade->exam ?? 'N/A',
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

    public function gradesByGroup(Group $group): StreamedResponse
    {
        $filename = 'notes_groupe_' . str_replace(' ', '_', $group->name) . '_' . date('Y-m-d') . '.csv';

        \App\Models\ActivityLog::log('exported', 'CSV', "Export des notes du groupe {$group->name} en CSV.");

        return new StreamedResponse(function () use ($group) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['Étudiant', 'Module', 'CC1', 'CC2', 'Exam', 'Note Finale', 'Résultat'], ';');

            $grades = Grade::with(['student.user', 'module'])
                ->whereIn('student_id', $group->students->pluck('id'))
                ->get();

            foreach ($grades as $grade) {
                fputcsv($handle, [
                    $grade->student->user->name ?? 'N/A',
                    $grade->module->name ?? 'N/A',
                    $grade->cc1 ?? 'N/A',
                    $grade->cc2 ?? 'N/A',
                    $grade->exam ?? 'N/A',
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

    public function gradesByModule(Module $module): StreamedResponse
    {
        $filename = 'notes_module_' . str_replace(' ', '_', $module->name) . '_' . date('Y-m-d') . '.csv';

        \App\Models\ActivityLog::log('exported', 'CSV', "Export des notes du module {$module->name} en CSV.");

        return new StreamedResponse(function () use ($module) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['Étudiant', 'Groupe', 'CC1', 'CC2', 'Exam', 'Note Finale', 'Résultat'], ';');

            $grades = Grade::with(['student.user', 'student.group'])
                ->where('module_id', $module->id)
                ->get();

            foreach ($grades as $grade) {
                fputcsv($handle, [
                    $grade->student->user->name ?? 'N/A',
                    $grade->student->group->name ?? 'N/A',
                    $grade->cc1 ?? 'N/A',
                    $grade->cc2 ?? 'N/A',
                    $grade->exam ?? 'N/A',
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
            
            fputcsv($handle, ['Étudiant', 'Date', 'Durée (h)', 'Justifiée', 'Motif'], ';');

            $absences = Absence::with(['student.user'])->get();
            foreach ($absences as $absence) {
                fputcsv($handle, [
                    $absence->student->user->name ?? 'N/A',
                    $absence->date ?? 'N/A',
                    $absence->duration ?? 'N/A',
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

    public function statistics(): StreamedResponse
    {
        $filename = 'statistiques_upf_' . date('Y-m-d') . '.csv';

        \App\Models\ActivityLog::log('exported', 'CSV', "Export des statistiques académiques en CSV.");

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Section 1: Stats par Filière
            fputcsv($handle, ['--- STATISTIQUES PAR FILIÈRE ---'], ';');
            fputcsv($handle, ['Filière', 'Nombre Étudiants', 'Nombre de Notes Saisies', 'Moyenne Générale', 'Taux de Réussite (%)'], ';');

            $filieres = Filiere::with('groups.students')->get();
            foreach ($filieres as $filiere) {
                $studentIds = $filiere->groups->flatMap(fn($g) => $g->students->pluck('id'))->toArray();
                $totalStudents = count($studentIds);

                $gradesQuery = Grade::whereIn('student_id', $studentIds);
                $totalGrades = $gradesQuery->count();
                $avgGrade = round($gradesQuery->avg('final_grade') ?? 0, 2);

                $validatedGrades = Grade::whereIn('student_id', $studentIds)->where('final_grade', '>=', 10)->count();
                $successRate = $totalGrades > 0 ? round(($validatedGrades / $totalGrades) * 100, 2) : 0;

                fputcsv($handle, [
                    $fereName = $filiere->name,
                    $totalStudents,
                    $totalGrades,
                    $avgGrade,
                    $successRate . '%'
                ], ';');
            }

            fputcsv($handle, [], ';'); // Ligne vide de séparation

            // Section 2: Stats par Groupe
            fputcsv($handle, ['--- STATISTIQUES PAR GROUPE ---'], ';');
            fputcsv($handle, ['Groupe', 'Filière', 'Niveau', 'Étudiants', 'Moyenne Générale', 'Taux de Réussite (%)'], ';');

            $groups = Group::with(['students', 'filiere'])->get();
            foreach ($groups as $group) {
                $studentIds = $group->students->pluck('id')->toArray();
                $totalStudents = count($studentIds);

                $gradesQuery = Grade::whereIn('student_id', $studentIds);
                $totalGrades = $gradesQuery->count();
                $avgGrade = round($gradesQuery->avg('final_grade') ?? 0, 2);

                $validatedGrades = Grade::whereIn('student_id', $studentIds)->where('final_grade', '>=', 10)->count();
                $successRate = $totalGrades > 0 ? round(($validatedGrades / $totalGrades) * 100, 2) : 0;

                fputcsv($handle, [
                    $group->name,
                    $group->filiere->name ?? 'N/A',
                    $group->level,
                    $totalStudents,
                    $avgGrade,
                    $successRate . '%'
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
