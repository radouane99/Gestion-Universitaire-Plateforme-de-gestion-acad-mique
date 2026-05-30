<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;

class ExamDisplayListController extends Controller
{
    /**
     * Show the display list for a specific exam.
     */
    public function show(Exam $exam)
    {
        $exam->load([
            'module', 
            'group.filiere', 
            'room', 
            'proctors.user', 
            'convocations.student.user'
        ]);

        // Dynamically assign seat numbers if empty
        $convocations = $exam->convocations()->with('student.user')->get()->sortBy('student.user.name');
        $seatNumber = 1;
        foreach ($convocations as $convocation) {
            if (empty($convocation->seat_number)) {
                $convocation->update([
                    'seat_number' => 'Place ' . $seatNumber
                ]);
            }
            $seatNumber++;
        }

        // Reload fresh convocations after updates
        $exam->load(['convocations.student.user']);

        return view('admin.exams.display_list.show', compact('exam'));
    }

    /**
     * Generate the PDF for the display list.
     */
    public function downloadPdf(Exam $exam)
    {
        $exam->load([
            'module', 
            'group.filiere', 
            'room', 
            'proctors.user', 
            'convocations.student.user'
        ]);

        // Dynamically assign seat numbers if empty
        $convocations = $exam->convocations()->with('student.user')->get()->sortBy('student.user.name');
        $seatNumber = 1;
        foreach ($convocations as $convocation) {
            if (empty($convocation->seat_number)) {
                $convocation->update([
                    'seat_number' => 'Place ' . $seatNumber
                ]);
            }
            $seatNumber++;
        }

        // Reload fresh convocations after updates
        $exam->load(['convocations.student.user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.exam_display_list', compact('exam'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('liste_affichage_' . $exam->module->name . '_' . $exam->date . '.pdf');
    }
}
