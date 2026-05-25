<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Textbook;
use App\Models\Schedule;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TextbookController extends Controller
{
    /**
     * Display all textbook entries for the active professor.
     */
    public function index()
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            return abort(403, 'Unauthorized.');
        }

        $entries = Textbook::where('professor_id', $professor->id)
            ->with(['group', 'module'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('professor.textbook.index', compact('entries'));
    }

    /**
     * Show the form to create a new session entry.
     */
    public function create()
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            return abort(403, 'Unauthorized.');
        }

        // Get modules and groups taught by the professor from their schedule
        $taught = Schedule::where('professor_id', $professor->id)
            ->with(['group', 'module'])
            ->get()
            ->unique(function ($item) {
                return $item->group_id . '-' . $item->module_id;
            });

        return view('professor.textbook.create', compact('taught'));
    }

    /**
     * Store a newly created entry.
     */
    public function store(Request $request)
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            return abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'module_id' => 'required|exists:modules,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'type' => 'required|in:Cours,TD,TP',
            'objective' => 'required|string|min:5',
        ]);

        $entry = Textbook::create([
            'professor_id' => $professor->id,
            'group_id' => $validated['group_id'],
            'module_id' => $validated['module_id'],
            'date' => now()->format('Y-m-d'), // Saisie automatique en lecture seule
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'type' => $validated['type'],
            'objective' => $validated['objective'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Textbook',
            'description' => "Enregistrement d'une séance dans le cahier de textes pour le module ID {$validated['module_id']}.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('professor.textbook.index')->with('success', 'Séance enregistrée avec succès dans le cahier de textes.');
    }

    /**
     * Admin view to consult all textbook entries from all professors.
     */
    public function adminIndex(Request $request)
    {
        $query = Textbook::with(['professor.user', 'group', 'module']);

        // Optional filtering
        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $entries = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $professors = \App\Models\Professor::with('user')->get();
        $groups = \App\Models\Group::all();

        return view('admin.textbooks.index', compact('entries', 'professors', 'groups'));
    }
}
