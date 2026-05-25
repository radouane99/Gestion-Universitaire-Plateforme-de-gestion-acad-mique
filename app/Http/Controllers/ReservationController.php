<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\ActivityLog;

class ReservationController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            return redirect()->route('dashboard')->with('error', 'Accès réservé aux professeurs.');
        }

        $reservations = Reservation::where('professor_id', $professor->id)
            ->with('room')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('professor.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('professor.reservations.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:1000',
        ]);

        $startDateTime = $validated['date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['date'] . ' ' . $validated['end_time'];

        $professor = Auth::user()->professor;
        if (!$professor) {
            return back()->withErrors(['error' => 'Profil professeur introuvable.'])->withInput();
        }

        // Bulleproof Server-Side Collision Check against other reservations
        $collision = Reservation::where('room_id', $validated['room_id'])
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        // Check against regular class schedules for the room
        $scheduleCollision = \App\Models\Schedule::where('room_id', $validated['room_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })->exists();

        // Check if the professor is already occupied (has another reservation at the same time)
        $profReservationCollision = Reservation::where('professor_id', $professor->id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        // Check if the professor is already occupied (has a class scheduled at the same time)
        $profScheduleCollision = \App\Models\Schedule::where('professor_id', $professor->id)
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($profReservationCollision || $profScheduleCollision) {
            return back()->withErrors(['room_id' => 'Refus : Vous avez déjà une réservation ou un cours prévu sur cette même période ! Vous ne pouvez pas être à deux endroits en même temps.'])->withInput();
        }

        if ($collision || $scheduleCollision) {
            $roomName = \App\Models\Room::find($validated['room_id'])->name ?? 'Inconnue';
            $admins = \App\Models\User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AcademicNotification(
                    "Tentative échouée : Le professeur " . Auth::user()->name . " a essayé de réserver la salle {$roomName} mais elle est indisponible.",
                    'error',
                    route('admin.reservations.index')
                ));
            }
            return back()->withErrors(['room_id' => 'Refus : La salle est indisponible sur cette période.'])->withInput();
        }

        $reservation = Reservation::create([
            'professor_id' => $professor->id,
            'room_id' => $validated['room_id'],
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'purpose' => $validated['purpose'],
            'status' => 'approved', // Auto-approuvé
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Reservation',
            'description' => "Réservation automatique de la salle '{$reservation->room->name}' le {$validated['date']} de {$validated['start_time']} à {$validated['end_time']}.",
            'ip_address' => $request->ip()
        ]);

        $admins = \App\Models\User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\AcademicNotification(
                "Nouvelle réservation confirmée par le professeur " . Auth::user()->name . " pour la salle {$reservation->room->name}.",
                'success',
                route('admin.reservations.index')
            ));
        }

        return redirect()->route('professor.reservations.index')->with('success', 'La salle était disponible. Votre réservation a été approuvée automatiquement !');
    }
}
