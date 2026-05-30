<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Module;
use App\Models\Grade;
use App\Traits\PVCompilerTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentController extends Controller
{
    use PVCompilerTrait;

    public function releveNotes(Student $student, AcademicYear $academicYear)
    {
        // Require eager loading
        $student->load(['user', 'group.filiere']);

        // Get semesters for the student's level
        $semesters = Semester::where('level', $student->group->level)->orderBy('name')->get();
        
        // Get all modules for these semesters
        $modules = Module::where('filiere_id', $student->group->filiere_id)
            ->whereIn('semester_id', $semesters->pluck('id'))
            ->orderBy('semester_id')
            ->orderBy('id')
            ->get();
            
        // Get grades for the student
        $grades = Grade::where('student_id', $student->id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->get()
            ->groupBy('student_id');
            
        // Compile PV data for the single student
        $pvData = $this->compilePVData(collect([$student]), $modules, $grades, true, $semesters);
        $studentData = $pvData[$student->id] ?? null;

        if (!$studentData) {
            abort(404, "Data non disponible pour ce relevé.");
        }

        // Mention calculation
        $annualAverage = $studentData['annual_average'];
        $mention = $this->calculateMention($annualAverage);

        // Dummy URL for verification
        $verificationUrl = url('/verify-document/releve/' . $student->id . '/' . $academicYear->id);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verificationUrl));

        $isPdf = true;
        $pdf = Pdf::loadView('pdf.releve_notes', compact(
            'student',
            'academicYear',
            'semesters',
            'modules',
            'studentData',
            'mention',
            'qrCode',
            'isPdf'
        ));

        $nameSlug = \Illuminate\Support\Str::slug($student->user->name, '_');
        return $pdf->download("releve_notes_{$nameSlug}.pdf");
    }

    public function attestationReussite(Student $student, AcademicYear $academicYear)
    {
        $student->load(['user', 'group.filiere']);

        // Get semesters for the student's level
        $semesters = Semester::where('level', $student->group->level)->orderBy('name')->get();
        
        // Get all modules for these semesters
        $modules = Module::where('filiere_id', $student->group->filiere_id)
            ->whereIn('semester_id', $semesters->pluck('id'))
            ->orderBy('semester_id')
            ->orderBy('id')
            ->get();
            
        // Get grades for the student
        $grades = Grade::where('student_id', $student->id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->get()
            ->groupBy('student_id');
            
        // Compile PV data for the single student
        $pvData = $this->compilePVData(collect([$student]), $modules, $grades, true, $semesters);
        $studentData = $pvData[$student->id] ?? null;

        if (!$studentData || !in_array($studentData['annual_decision'], ['Admis', 'Diplômé'])) {
            return redirect()->back()->with('error', "Cet étudiant n'est pas admis. L'attestation de réussite ne peut pas être générée.");
        }

        // Mention calculation
        $annualAverage = $studentData['annual_average'];
        $mention = $this->calculateMention($annualAverage);

        // Dummy URL for verification
        $verificationUrl = url('/verify-document/attestation/' . $student->id . '/' . $academicYear->id);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verificationUrl));

        $isPdf = true;
        $pdf = Pdf::loadView('pdf.attestation_reussite', compact(
            'student',
            'academicYear',
            'studentData',
            'mention',
            'qrCode',
            'isPdf'
        ));

        // Use landscape format for certificate
        $pdf->setPaper('A4', 'landscape');

        $nameSlug = \Illuminate\Support\Str::slug($student->user->name, '_');
        return $pdf->download("attestation_reussite_{$nameSlug}.pdf");
    }

    private function calculateMention($average)
    {
        if ($average === null) return '-';
        if ($average >= 16) return 'Très Bien';
        if ($average >= 14) return 'Bien';
        if ($average >= 12) return 'Assez Bien';
        if ($average >= 10) return 'Passable';
        return '-';
    }
}
