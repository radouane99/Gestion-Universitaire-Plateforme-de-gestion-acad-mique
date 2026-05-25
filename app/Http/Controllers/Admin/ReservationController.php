<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Professor;
use App\Models\ActivityLog;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room', 'professor.user'])
            ->orderBy('start_time', 'desc')
            ->get();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $rooms = Room::all();
        $professors = Professor::with('user')->get();
        return view('admin.reservations.create', compact('rooms', 'professors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $startDateTime = $validated['date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['date'] . ' ' . $validated['end_time'];

        // Bulletproof overlap check
        $collision = Reservation::where('room_id', $validated['room_id'])
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        if ($collision) {
            return back()->withErrors(['room_id' => 'Cette salle est déjà réservée sur ce créneau horaire.'])->withInput();
        }

        $reservation = Reservation::create([
            'professor_id' => $validated['professor_id'],
            'room_id' => $validated['room_id'],
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'purpose' => $validated['purpose'],
            'status' => $validated['status'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Reservation',
            'description' => "Administration : Création d'une réservation (Salle: {$reservation->room->name}, Professeur: {$reservation->professor->user->name}, Statut: {$reservation->status}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Réservation créée avec succès.');
    }

    public function edit(Reservation $reservation)
    {
        $rooms = Room::all();
        $professors = Professor::with('user')->get();
        return view('admin.reservations.edit', compact('reservation', 'rooms', 'professors'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $startDateTime = $validated['date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['date'] . ' ' . $validated['end_time'];

        // Bulletproof overlap check (excluding the current reservation)
        $collision = Reservation::where('room_id', $validated['room_id'])
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        if ($collision) {
            return back()->withErrors(['room_id' => 'Cette salle est déjà réservée sur ce créneau horaire.'])->withInput();
        }

        $reservation->update([
            'professor_id' => $validated['professor_id'],
            'room_id' => $validated['room_id'],
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'purpose' => $validated['purpose'],
            'status' => $validated['status'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Reservation',
            'description' => "Administration : Mise à jour de la réservation ID {$reservation->id} (Salle: {$reservation->room->name}, Prof: {$reservation->professor->user->name}, Statut: {$reservation->status}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Réservation mise à jour avec succès.');
    }

    public function destroy(Reservation $reservation)
    {
        $roomName = $reservation->room->name;
        $profName = $reservation->professor->user->name;
        $reservation->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Reservation',
            'description' => "Administration : Supprimé/Annulé la réservation de la salle {$roomName} par {$profName}.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Réservation supprimée et annulée avec succès.');
    }

    public function approve(Reservation $reservation)
    {
        $reservation->update(['status' => 'approved']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Reservation',
            'description' => "Administration : Approuvé la réservation de la salle {$reservation->room->name} pour {$reservation->professor->user->name}.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Demande de réservation approuvée avec succès !');
    }

    public function reject(Reservation $reservation)
    {
        $reservation->update(['status' => 'rejected']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Reservation',
            'description' => "Administration : Rejeté la réservation de la salle {$reservation->room->name} pour {$reservation->professor->user->name}.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Demande de réservation rejetée.');
    }
}
