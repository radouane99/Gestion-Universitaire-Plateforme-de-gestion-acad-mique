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

        $professors = User::whereHas('role', function($q) {
            $q->where('name', 'professor');
        })->orderBy('name')->get();

        return view('student.appointments', compact('myAppointments', 'availableSlots', 'professors'));
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
            ->whereIn('status', ['available', 'booked'])
            ->with(['appointments.student.user'])
            ->orderBy('start_time', 'desc')
            ->get();

        $pendingRequests = Appointment::whereHas('slot', function($q) use ($user) {
                $q->where('host_id', $user->id);
            })
            ->where('status', 'requested')
            ->with(['student.user', 'slot'])
            ->orderBy('created_at', 'desc')
            ->get();

        $routePrefix = $user->isAdmin() ? 'admin.' : 'professor.';

        return view('host.appointments', compact('mySlots', 'pendingRequests', 'routePrefix'));
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

    /**
     * Génération automatique de créneaux par défaut pour l'Administration
     */
    public function generateDefaultSlots(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'generation_date' => 'required|date|after_or_equal:today',
        ]);

        $dateString = $validated['generation_date'];
        
        // Define default 30min slot ranges (excluding 13:00 - 14:00 lunch break)
        $slotsData = [
            ['10:00', '10:30'],
            ['10:30', '11:00'],
            ['11:00', '11:35'],
            ['11:35', '12:10'],
            ['12:10', '12:45'],
            ['12:45', '13:20'], // Adjust slightly to match half hours precisely
            // Pause 13h00 - 14h00
            ['14:00', '14:30'],
            ['14:30', '15:00'],
            ['15:00', '15:30'],
            ['15:30', '16:00'],
            ['16:00', '16:30']
        ];

        // Let's use clean 30 minutes slots:
        $slotsData = [
            ['10:00', '10:30'],
            ['10:30', '11:00'],
            ['11:00', '11:30'],
            ['11:30', '12:00'],
            ['12:00', '12:30'],
            ['12:30', '13:00'],
            // Pause 13h00 - 14h00
            ['14:00', '14:30'],
            ['14:30', '15:00'],
            ['15:00', '15:30'],
            ['15:30', '16:00'],
            ['16:00', '16:30']
        ];

        $createdCount = 0;
        foreach ($slotsData as $slotTime) {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$dateString} {$slotTime[0]}");
            $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$dateString} {$slotTime[1]}");

            // Check if slot already exists for this host at start_time
            $exists = AppointmentSlot::where('host_id', $user->id)
                ->where('start_time', $start)
                ->exists();

            if (!$exists) {
                AppointmentSlot::create([
                    'host_id' => $user->id,
                    'start_time' => $start,
                    'end_time' => $end,
                    'status' => 'available'
                ]);
                $createdCount++;
            }
        }

        ActivityLog::log('created', 'AppointmentSlot', "Génération en masse de {$createdCount} créneaux pour le {$dateString} par l'administrateur #{$user->id}.");

        return back()->with('success', "{$createdCount} créneaux de disponibilité ont été générés pour la journée du " . \Carbon\Carbon::parse($dateString)->format('d/m/Y') . ".");
    }

    /**
     * Proposer une demande directe de RDV à un Professeur (Étudiant)
     */
    public function requestDirect(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        $validated = $request->validate([
            'professor_id' => 'required|exists:users,id',
            'proposed_time' => 'required|date|after:now',
            'purpose' => 'required|string|max:500',
        ]);

        $start = \Carbon\Carbon::parse($validated['proposed_time']);
        $end = (clone $start)->addMinutes(30);

        // Create virtual slot for this direct request
        $slot = AppointmentSlot::create([
            'host_id' => $validated['professor_id'],
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'requested',
        ]);

        $appointment = Appointment::create([
            'student_id' => $student->id,
            'appointment_slot_id' => $slot->id,
            'purpose' => $validated['purpose'],
            'status' => 'requested',
        ]);

        // Notify the professor
        $professorUser = User::find($validated['professor_id']);
        $professorUser->notify(new AcademicNotification(
            "📅 Demande directe de rendez-vous de l'étudiant {$student->user->name} le {$start->format('d/m/Y \à H:i')}",
            'info',
            route('professor.appointments.index')
        ));

        ActivityLog::log('created', 'Appointment', "Demande directe de RDV #{$appointment->id} soumise par l'étudiant #{$student->id} au professeur #{$validated['professor_id']}.");

        return back()->with('success', 'Votre proposition de rendez-vous a été envoyée au professeur. Vous serez notifié de sa décision.');
    }

    /**
     * Confirmer la demande directe de l'étudiant (Professeur ou Admin)
     */
    public function acceptRequest(Appointment $appointment)
    {
        $user = Auth::user();
        $slot = $appointment->slot;

        if ($slot->host_id !== $user->id) {
            abort(403);
        }

        $appointment->update(['status' => 'scheduled']);
        $slot->update(['status' => 'booked']);

        // Notify student
        $appointment->student->user?->notify(new AcademicNotification(
            "✓ Votre demande de rendez-vous du {$slot->start_time->format('d/m/Y \à H:i')} a été acceptée par l'intervenant.",
            'success',
            route('student.appointments.index')
        ));

        ActivityLog::log('updated', 'Appointment', "Rendez-vous direct #{$appointment->id} accepté par l'hôte #{$user->id}.");

        return back()->with('success', 'Vous avez accepté et planifié ce rendez-vous.');
    }

    /**
     * Décliner et proposer une contre-proposition (Professeur ou Admin)
     */
    public function suggestAlternative(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        $oldSlot = $appointment->slot;

        if ($oldSlot->host_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'suggested_time' => 'required|date|after:now',
        ]);

        $start = \Carbon\Carbon::parse($validated['suggested_time']);
        $end = (clone $start)->addMinutes(30);

        // Delete old requested slot
        $oldSlot->delete();

        // Create new suggested slot
        $newSlot = AppointmentSlot::create([
            'host_id' => $user->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'suggested',
        ]);

        $appointment->update([
            'appointment_slot_id' => $newSlot->id,
            'status' => 'suggested',
        ]);

        // Notify student
        $appointment->student->user?->notify(new AcademicNotification(
            "🔄 Contre-proposition de rendez-vous reçue le {$start->format('d/m/Y \à H:i')}. Veuillez valider ou décliner.",
            'warning',
            route('student.appointments.index')
        ));

        ActivityLog::log('updated', 'Appointment', "Contre-proposition pour le RDV #{$appointment->id} soumise par l'hôte #{$user->id} pour le {$start}.");

        return back()->with('success', 'Votre contre-proposition a été envoyée à l\'étudiant.');
    }

    /**
     * Valider la contre-proposition du Professeur (Étudiant)
     */
    public function confirmSuggestion(Appointment $appointment)
    {
        $student = Auth::user()->student;
        if (!$student || $appointment->student_id !== $student->id) {
            abort(403);
        }

        $slot = $appointment->slot;
        if ($appointment->status !== 'suggested') {
            return back()->with('error', 'Cette action n\'est pas autorisée.');
        }

        $appointment->update(['status' => 'scheduled']);
        $slot->update(['status' => 'booked']);

        // Notify host
        $hostRoute = $slot->host->isProfessor() ? route('professor.appointments.index') : route('admin.appointments.index');
        $slot->host->notify(new AcademicNotification(
            "✓ L'étudiant {$student->user->name} a accepté votre contre-proposition de rendez-vous pour le {$slot->start_time->format('d/m/Y \à H:i')}",
            'success',
            $hostRoute
        ));

        ActivityLog::log('updated', 'Appointment', "L'étudiant #{$student->id} a validé la contre-proposition du RDV #{$appointment->id}.");

        return back()->with('success', 'Rendez-vous planifié et confirmé avec succès !');
    }
}
