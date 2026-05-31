<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Student;
use App\Models\User;
use App\Models\ActivityLog;
use App\Notifications\AcademicNotification;

class AppointmentController extends Controller
{
    /**
     * Espace Étudiant : Suivi et Réservation de RDV
     */
    public function studentIndex()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Accès réservé aux étudiants.');
        }

        $myAppointments = Appointment::where('student_id', $student->id)
            ->with(['slot.host'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get available future slots
        $availableSlots = AppointmentSlot::where('status', 'available')
            ->where('start_time', '>', now())
            ->with('host')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('student.appointments', compact('myAppointments', 'availableSlots'));
    }

    /**
     * Espace Enseignant / Administrateur : Gestion des créneaux de disponibilité et RDV programmés
     */
    public function hostIndex()
    {
        $user = Auth::user();
        if (!$user->isProfessor() && !$user->isAdmin()) {
            abort(403, 'Accès réservé aux enseignants et administrateurs.');
        }

        $mySlots = AppointmentSlot::where('host_id', $user->id)
            ->with(['appointments.student.user'])
            ->orderBy('start_time', 'desc')
            ->get();

        $routePrefix = $user->isAdmin() ? 'admin.' : 'professor.';

        return view('host.appointments', compact('mySlots', 'routePrefix'));
    }

    /**
     * Création d'un créneau par le Professeur ou Admin
     */
    public function storeSlot(Request $request)
    {
        $user = Auth::user();
        if (!$user->isProfessor() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        AppointmentSlot::create([
            'host_id' => $user->id,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'available',
        ]);

        ActivityLog::log('created', 'AppointmentSlot', "Créneau de disponibilité créé par l'utilisateur #{$user->id} de {$validated['start_time']} à {$validated['end_time']}.");

        return back()->with('success', 'Créneau de disponibilité ajouté avec succès.');
    }

    /**
     * Réservation d'un créneau par l'Étudiant
     */
    public function book(Request $request, AppointmentSlot $slot)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        if ($slot->status !== 'available' || $slot->start_time <= now()) {
            return back()->with('error', 'Ce créneau n\'est plus disponible.');
        }

        $validated = $request->validate([
            'purpose' => 'required|string|max:500',
        ]);

        // Check if student already booked this slot
        $alreadyBooked = Appointment::where('student_id', $student->id)
            ->where('appointment_slot_id', $slot->id)
            ->where('status', 'scheduled')
            ->exists();

        if ($alreadyBooked) {
            return back()->with('error', 'Vous avez déjà réservé ce créneau.');
        }

        $appointment = Appointment::create([
            'student_id' => $student->id,
            'appointment_slot_id' => $slot->id,
            'purpose' => $validated['purpose'],
            'status' => 'scheduled',
        ]);

        $slot->update(['status' => 'booked']);

        // Notify the host
        $hostRoute = $slot->host->isProfessor() ? route('professor.appointments.index') : route('admin.appointments.index');
        $slot->host->notify(new AcademicNotification(
            "📅 Nouvel entretien réservé par l'étudiant {$student->user->name} le {$slot->start_time->format('d/m/Y \à H:i')}",
            'info',
            $hostRoute
        ));

        ActivityLog::log('created', 'Appointment', "Rendez-vous #{$appointment->id} réservé par l'étudiant #{$student->id} avec l'hôte #{$slot->host_id}.");

        return back()->with('success', 'Votre rendez-vous a été planifié avec succès !');
    }

    /**
     * Annulation du Rendez-vous par l'un des participants
     */
    public function cancel(Appointment $appointment)
    {
        $user = Auth::user();
        $student = $user->student;
        $slot = $appointment->slot;

        if (($student && $appointment->student_id === $student->id) || $slot->host_id === $user->id) {
            
            $appointment->update(['status' => 'cancelled']);
            $slot->update(['status' => 'available']);

            // Notify counterpart
            if ($student) {
                // Notified host
                $hostRoute = $slot->host->isProfessor() ? route('professor.appointments.index') : route('admin.appointments.index');
                $slot->host->notify(new AcademicNotification(
                    "🔴 Le rendez-vous du {$slot->start_time->format('d/m/Y \à H:i')} a été annulé par l'étudiant {$user->name}.",
                    'danger',
                    $hostRoute
                ));
            } else {
                // Notified student
                $appointment->student->user?->notify(new AcademicNotification(
                    "🔴 Votre rendez-vous du {$slot->start_time->format('d/m/Y \à H:i')} a été annulé par l'intervenant.",
                    'danger',
                    route('student.appointments.index')
                ));
            }

            ActivityLog::log('updated', 'Appointment', "Rendez-vous #{$appointment->id} annulé par l'utilisateur #{$user->id}.");

            return back()->with('success', 'Le rendez-vous a été annulé avec succès.');
        }

        abort(403);
    }

    /**
     * Supprimer un créneau vide par l'hôte
     */
    public function destroySlot(AppointmentSlot $slot)
    {
        $user = Auth::user();
        if ($slot->host_id !== $user->id) {
            abort(403);
        }

        if ($slot->status === 'booked') {
            return back()->with('error', 'Impossible de supprimer un créneau déjà réservé.');
        }

        $slot->delete();

        return back()->with('success', 'Le créneau a été retiré.');
    }
}
