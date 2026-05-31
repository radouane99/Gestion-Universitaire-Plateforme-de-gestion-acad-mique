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
    public function downloadPdf(Request $request, DocRequest $documentRequest)
    {
        if ($documentRequest->status !== 'approved') {
            return abort(403, 'Document non approuvé.');
        }

        if (!Auth::user()->isAdmin() && Auth::id() !== $documentRequest->user_id) {
            return abort(403, 'Action non autorisée.');
        }

        $requestData = $documentRequest->load('user');
        $setting = Setting::first() ?? new Setting();
        
        $verifyUrl = route('documents.verify', ['id' => $requestData->id, 'hash' => md5($requestData->id . $requestData->created_at)]);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $viewData = [
            'request' => $requestData,
            'setting' => $setting,
            'qrCode' => $qrCode
        ];
        $viewData['isPdf'] = true;

        if ($requestData->type == 'Transcript' || $requestData->type == 'Relevé de Notes') {
            $student = $requestData->user->student;
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
        } elseif ($requestData->type == 'Attestation de Travail') {
            $viewName = 'documents.work_certificate';
        } elseif ($requestData->type == 'Ordre de Mission') {
            $viewName = 'documents.mission_order';
        } elseif ($requestData->type == 'Convention de Stage') {
            $viewName = 'documents.internship_agreement';
        } else {
            $viewName = 'documents.certificate';
        }

        $pdf = Pdf::loadView($viewName, $viewData);
        $pdf->setPaper('A4', 'portrait');

        $nameSlug = \Illuminate\Support\Str::slug($requestData->user->name, '_');
        if ($requestData->type == 'Attestation de Travail') {
            $fileName = "attestation_travail_{$nameSlug}.pdf";
        } elseif ($requestData->type == 'Ordre de Mission') {
            $fileName = "ordre_mission_{$nameSlug}.pdf";
        } elseif ($requestData->type == 'Convention de Stage') {
            $fileName = "convention_stage_{$nameSlug}.pdf";
        } elseif ($requestData->type == 'Transcript' || $requestData->type == 'Relevé de Notes') {
            $fileName = "releve_notes_{$nameSlug}.pdf";
        } else {
            $fileName = "attestation_scolarite_{$nameSlug}.pdf";
        }
        
        if ($request->query('preview') == 1) {
            return $pdf->stream($fileName);
        }
        return $pdf->download($fileName);
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
