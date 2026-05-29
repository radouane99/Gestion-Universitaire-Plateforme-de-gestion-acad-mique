<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Services\ExamPlanningService;
use App\Services\ExamConvocationService;

class ExamPlanningController extends Controller
{
    /**
     * Show the simulation interface for a specific exam session
     */
    public function simulation(ExamSession $session)
    {
        $session->load('exams.module', 'exams.group', 'exams.room', 'exams.proctors.user');
        
        $stats = [
            'total_exams' => $session->exams->count(),
            'total_convocations' => \App\Models\Convocation::whereIn('exam_id', $session->exams->pluck('id'))->count(),
        ];

        return view('admin.exams.planning.simulation', compact('session', 'stats'));
    }

    /**
     * Generate / Simulate the planning for a session
     */
    public function generate(Request $request, ExamSession $session, ExamPlanningService $planningService)
    {
        $results = $planningService->generatePlanning($session);

        if (count($results['unplanned']) > 0 || count($results['conflicts']) > 0) {
            return redirect()->route('admin.exams.planning.simulation', $session)
                ->with('warning', 'Simulation terminée avec des conflits ou modules non planifiés.')
                ->with('results', $results);
        }

        return redirect()->route('admin.exams.planning.simulation', $session)
            ->with('success', 'Simulation réussie. ' . $results['planned'] . ' examens planifiés.');
    }

    /**
     * Validate the simulated planning
     */
    public function validatePlanning(ExamSession $session)
    {
        $session->update(['status' => 'validated']);
        return redirect()->route('admin.exams.planning.simulation', $session)
            ->with('success', 'Planning validé avec succès. Vous pouvez maintenant générer les convocations.');
    }

    /**
     * Generate convocations and assign seats
     */
    public function generateConvocations(ExamSession $session, ExamConvocationService $convocationService)
    {
        if ($session->status !== 'validated' && $session->status !== 'published') {
            return back()->with('error', 'Le planning doit être validé avant de générer les convocations.');
        }

        $results = $convocationService->generateConvocationsForSession($session);

        return redirect()->route('admin.exams.planning.simulation', $session)
            ->with('success', "Convocations générées : {$results['students']} pour étudiants, {$results['professors']} pour surveillants.");
    }

    /**
     * Publish the planning
     */
    public function publish(ExamSession $session)
    {
        $session->update(['status' => 'published']);
        return redirect()->route('admin.exams.planning.simulation', $session)
            ->with('success', 'Planning publié ! Les étudiants et professeurs peuvent désormais le voir.');
    }
}
