<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Group;
use App\Models\Module;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getGroups(Filiere $filiere)
    {
        return response()->json(
            $filiere->groups()->orderBy('name')->get(['id', 'name', 'level'])
        );
    }

    public function getModules(Group $group)
    {
        $moduleIds = Schedule::where('group_id', $group->id)
            ->pluck('module_id')
            ->unique();

        if ($moduleIds->isEmpty() && $group->filiere_id) {
            $modules = Module::where('filiere_id', $group->filiere_id)
                ->orderBy('name')
                ->get(['id', 'name', 'code']);
        } else {
            $modules = Module::whereIn('id', $moduleIds)
                ->orderBy('name')
                ->get(['id', 'name', 'code']);
        }

        return response()->json($modules);
    }

    public function getRoomAvailability(Room $room, Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $dayOfWeek = date('N', strtotime($date));

        $schedules = Schedule::where('room_id', $room->id)
            ->where('day_of_week', $dayOfWeek)
            ->with(['module', 'group', 'professor.user'])
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'Cours',
                    'start' => date('H:i', strtotime($s->start_time)),
                    'end' => date('H:i', strtotime($s->end_time)),
                    'details' => $s->module->name . ' (' . $s->group->name . ')',
                    'color' => 'bg-blue-500'
                ];
            });

        $filteredReservations = Reservation::where('room_id', $room->id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($date) {
                $query->whereRaw("DATE(start_time) = ?", [$date])
                    ->orWhereRaw("DATE(end_time) = ?", [$date]);
            })
            ->with('professor.user')
            ->get()
            ->map(function ($r) {
                $statusColor = $r->status === 'approved' ? 'bg-amber-500' : 'bg-gray-400';
                $statusText = $r->status === 'approved' ? 'Réservé' : 'En attente';
                return [
                    'type' => $statusText,
                    'start' => date('H:i', strtotime($r->start_time)),
                    'end' => date('H:i', strtotime($r->end_time)),
                    'details' => ($r->professor->user->name ?? 'N/A') . ' - ' . $r->purpose,
                    'color' => $statusColor
                ];
            });

        $all = $schedules->concat($filteredReservations)->sortBy('start')->values();
        return response()->json($all);
    }
}
