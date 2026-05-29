<?php

namespace App\Http\Controllers;

use App\Models\Request as DocRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentController extends Controller
{
    public function downloadPdf(DocRequest $documentRequest)
    {
        if ($documentRequest->status !== 'approved') {
            return abort(403, 'Document non approuvé.');
        }

        if (!Auth::user()->isAdmin() && Auth::id() !== $documentRequest->user_id) {
            return abort(403, 'Action non autorisée.');
        }

        $request = $documentRequest->load('user');
        $setting = Setting::first() ?? new Setting();
        
        $verifyUrl = route('documents.verify', ['id' => $request->id, 'hash' => md5($request->id . $request->created_at)]);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $viewData = compact('request', 'setting', 'qrCode');

        if ($request->type == 'Transcript' || $request->type == 'Relevé de Notes') {
            $student = $request->user->student;
            $gradesBySemester = collect();
            $yearlyGPA = 0;
            $isAnnual = false;
            $transcriptTitle = 'RELEVÉ DE NOTES';
            
            if ($student) {
                // Fetch student active grades (non-archived)
                $grades = \App\Models\Grade::where('student_id', $student->id)
                    ->where('is_archived', false)
                    ->with(['module.semester'])
                    ->get();
                
                // Group grades by semester
                $rawGradesBySemester = $grades->groupBy(function($g) {
                    return $g->module && $g->module->semester ? $g->module->semester->name : 'Autres';
                })->sortKeys();
                
                // Filter: only keep semesters that have at least one module with a non-null grade
                $gradesBySemester = $rawGradesBySemester->filter(function($semGrades) {
                    return $semGrades->whereNotNull('final_grade')->count() > 0;
                });
                
                $totalGPA = 0;
                $validSemestersCount = 0;
                foreach($gradesBySemester as $sem => $semGrades) {
                    $semGPA = $semGrades->whereNotNull('final_grade')->avg('final_grade');
                    $totalGPA += $semGPA;
                    $validSemestersCount++;
                }
                
                $yearlyGPA = $validSemestersCount > 0 ? $totalGPA / $validSemestersCount : 0;
                $isAnnual = $validSemestersCount > 1;
                
                if ($isAnnual) {
                    $transcriptTitle = 'RELEVÉ DE NOTES ANNUEL';
                } elseif ($validSemestersCount === 1) {
                    $semName = $gradesBySemester->keys()->first();
                    $transcriptTitle = 'RELEVÉ DE NOTES - SEMESTRE ' . $semName;
                } else {
                    // Fallback to raw grades if no grades entered yet
                    $gradesBySemester = $rawGradesBySemester;
                    $transcriptTitle = 'RELEVÉ DE NOTES';
                }
            }
            
            $viewData['student'] = $student;
            $viewData['gradesBySemester'] = $gradesBySemester;
            $viewData['yearlyGPA'] = $yearlyGPA;
            $viewData['isAnnual'] = $isAnnual;
            $viewData['transcriptTitle'] = $transcriptTitle;
            $viewName = 'documents.transcript';
        } elseif ($request->type == 'Attestation de Travail') {
            $viewName = 'documents.work_certificate';
        } elseif ($request->type == 'Ordre de Mission') {
            $viewName = 'documents.mission_order';
        } elseif ($request->type == 'Convention de Stage') {
            $viewName = 'documents.internship_agreement';
        } else {
            $viewName = 'documents.certificate';
        }

        $pdf = Pdf::loadView($viewName, $viewData);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('document_' . $request->id . '.pdf');
    }

    public function verify($id, $hash)
    {
        $request = DocRequest::with('user')->findOrFail($id);
        
        if (md5($request->id . $request->created_at) !== $hash || $request->status !== 'approved') {
            return abort(404, 'Document invalide ou non trouvé.');
        }

        return view('documents.verify', compact('request'));
    }
}
