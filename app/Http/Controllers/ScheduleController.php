<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Group;
use App\Models\Module;
use App\Models\Professor;
use App\Models\Room;
use App\Models\ActivityLog;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['group', 'module', 'professor.user', 'room'])->get();
        
        // Fetch groups and modules for filtering options in the view
        $groups = Group::all();
        $modules = Module::all();

        return view('admin.schedules.index', compact('schedules', 'groups', 'modules'));
    }

    public function create()
    {
        $groups = Group::all();
        $modules = Module::all();
        $professors = Professor::with('user')->get();
        $rooms = Room::all();
        return view('admin.schedules.create', compact('groups', 'modules', 'professors', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'module_id' => 'required|exists:modules,id',
            'professor_id' => 'required|exists:professors,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Bulletproof Collision Check against other schedules and approved reservations
        $scheduleCollision = Schedule::where('room_id', $validated['room_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })->exists();

        $startDateTime = $validated['date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['date'] . ' ' . $validated['end_time'];

        $reservationCollision = \App\Models\Reservation::where('room_id', $validated['room_id'])
            ->whereIn('status', ['approved'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        if ($scheduleCollision || $reservationCollision) {
            return back()->withErrors(['room_id' => 'Action refusée : Cette salle est déjà occupée par un autre cours ou une réservation validée sur ce créneau horaire.'])->withInput();
        }

        $validated['day_of_week'] = date('N', strtotime($validated['date']));

        $schedule = Schedule::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Schedule',
            'description' => "Planification d'une nouvelle séance pour le groupe '{$schedule->group->name}' - Module '{$schedule->module->name}' à la date du " . date('d/m/Y', strtotime($schedule->date)) . ".",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Séance planifiée avec succès.');
    }

    public function edit(Schedule $schedule)
    {
        $groups = Group::all();
        $modules = Module::all();
        $professors = Professor::with('user')->get();
        $rooms = Room::all();
        return view('admin.schedules.edit', compact('schedule', 'groups', 'modules', 'professors', 'rooms'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'module_id' => 'required|exists:modules,id',
            'professor_id' => 'required|exists:professors,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Bulletproof Collision Check for Update (excluding self)
        $scheduleCollision = Schedule::where('room_id', $validated['room_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })->exists();

        $startDateTime = $validated['date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['date'] . ' ' . $validated['end_time'];

        $reservationCollision = \App\Models\Reservation::where('room_id', $validated['room_id'])
            ->whereIn('status', ['approved'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        if ($scheduleCollision || $reservationCollision) {
            return back()->withErrors(['room_id' => 'Action refusée : Cette salle est déjà occupée par un autre cours ou une réservation validée sur ce créneau horaire.'])->withInput();
        }

        $validated['day_of_week'] = date('N', strtotime($validated['date']));

        $schedule->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Schedule',
            'description' => "Modification de la séance d'emploi du temps (Groupe: {$schedule->group->name}, Module: {$schedule->module->name}, Prof: {$schedule->professor->user->name}, Salle: {$schedule->room->name}, Date: " . date('d/m/Y', strtotime($schedule->date)) . ").",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Séance d\'emploi du temps mise à jour avec succès.');
    }

    public function destroy(Schedule $schedule)
    {
        $groupName = $schedule->group->name;
        $moduleName = $schedule->module->name;
        
        $schedule->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Schedule',
            'description' => "Annulation/Suppression d'une séance programmée (Groupe: {$groupName}, Module: {$moduleName}).",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Séance d\'emploi du temps annulée avec succès.');
    }

    public function calendar()
    {
        return view('academic-calendar');
    }

    /**
     * Student's personal schedule (filtered by their group)
     */
    public function studentSchedule()
    {
        $student = Auth::user()->student;
        if (!$student) abort(403);

        $schedules = Schedule::where('group_id', $student->group_id)
            ->with(['module', 'professor.user', 'room', 'group'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $byDay = $schedules->groupBy('day_of_week');
        $group = $student->group;

        return view('student.schedule', compact('schedules', 'byDay', 'group'));
    }

    /**
     * Professor's personal schedule (sessions they teach)
     */
    public function professorSchedule()
    {
        $professor = Auth::user()->professor;
        if (!$professor) abort(403);

        $schedules = Schedule::where('professor_id', $professor->id)
            ->with(['module', 'group.filiere', 'room'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $byDay      = $schedules->groupBy('day_of_week');
        $totalGroups = $schedules->pluck('group_id')->unique()->count();
        $totalHours  = $schedules->sum(fn($s) => 
            (strtotime($s->end_time) - strtotime($s->start_time)) / 3600
        );

        return view('professor.schedule', compact('schedules', 'byDay', 'totalGroups', 'totalHours'));
    }

    /**
     * Export the authenticated user's schedule as PDF
     */
    public function exportPdf()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            $student = $user->student;
            $schedules = Schedule::where('group_id', $student->group_id)
                ->with(['module', 'professor.user', 'room', 'group'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
            $title = "Emploi du temps - Étudiant : " . $user->name;
        } elseif ($user->isProfessor()) {
            $professor = $user->professor;
            $schedules = Schedule::where('professor_id', $professor->id)
                ->with(['module', 'group.filiere', 'room'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
            $title = "Emploi du temps - Professeur : " . $user->name;
        } else {
            abort(403, 'Unauthorized action.');
        }

        $byDay = $schedules->groupBy('day_of_week');
        $days = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('schedules.pdf', compact('schedules', 'byDay', 'days', 'title', 'user'));
        
        return $pdf->download('emploi_du_temps_' . strtolower(str_replace(' ', '_', $user->name)) . '.pdf');
    }
}
