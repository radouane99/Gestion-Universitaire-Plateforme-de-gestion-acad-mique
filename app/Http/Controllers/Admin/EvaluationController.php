<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModuleEvaluation;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Display aggregated analytics and comments of all module evaluations.
     */
    public function index()
    {
        // 1. Get the toggle state
        $isEvaluationOpen = Setting::get('evaluation_open', false);

        // 2. Aggregate evaluations by module and professor
        $analytics = ModuleEvaluation::select(
            'module_id',
            'professor_id',
            DB::raw('COUNT(*) as total_responses'),
            DB::raw('AVG(q1_rating) as q1_avg'),
            DB::raw('AVG(q2_rating) as q2_avg'),
            DB::raw('AVG(q3_rating) as q3_avg'),
            DB::raw('AVG(q4_rating) as q4_avg'),
            DB::raw('(AVG(q1_rating) + AVG(q2_rating) + AVG(q3_rating) + AVG(q4_rating)) / 4 as overall_avg')
        )
        ->groupBy('module_id', 'professor_id')
        ->with(['module', 'professor.user'])
        ->get();

        // 3. Get all qualitative feedback (comments)
        $comments = ModuleEvaluation::whereNotNull('comment')
            ->where('comment', '!=', '')
            ->with(['module', 'professor.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.evaluations.index', compact('analytics', 'comments', 'isEvaluationOpen'));
    }

    /**
     * Toggle evaluation period state globally.
     */
    public function toggle(Request $request)
    {
        $settings = Setting::first();
        if (!$settings) {
            $settings = Setting::create([
                'institution_name' => 'Université Privée de Fès',
                'evaluation_open' => false
            ]);
        }

        $newState = !$settings->evaluation_open;
        $settings->update([
            'evaluation_open' => $newState
        ]);

        ActivityLog::log(
            'updated',
            'Setting',
            "La campagne d'évaluation anonyme des enseignements a été " . ($newState ? 'ouverte' : 'fermée') . " par l'administration."
        );

        return back()->with('success', "La période d'évaluation a été " . ($newState ? 'ouverte' : 'fermée') . " avec succès !");
    }
}
