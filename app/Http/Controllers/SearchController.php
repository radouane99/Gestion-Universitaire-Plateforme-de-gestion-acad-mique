<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Module;
use App\Models\Professor;
use App\Models\Request as AcademicRequest;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        
        if (strlen($q) < 2) {
            return response()->json([
                'students' => [],
                'professors' => [],
                'modules' => [],
                'rooms' => [],
                'exams' => [],
                'requests' => [],
            ]);
        }

        // 1. Search Students
        $students = Student::with(['user', 'group'])
            ->whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->orWhere('student_number', 'like', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($s) => [
                'title' => $s->user?->name ?? 'Étudiant sans nom',
                'subtitle' => "Matricule: {$s->student_number} • " . ($s->group?->name ?? 'Sans groupe'),
                'url' => route('admin.discipline.index') . "?search=" . urlencode($s->student_number), // Redirect to dashboard relevance
            ]);

        // 2. Search Professors
        $professors = Professor::with(['user'])
            ->whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->orWhere('department', 'like', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'title' => $p->user?->name ?? 'Professeur sans nom',
                'subtitle' => "Département: {$p->department}",
                'url' => '#',
            ]);

        // 3. Search Modules
        $modules = Module::where('name', 'like', "%{$q}%")
            ->orWhere('code', 'like', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($m) => [
                'title' => $m->name,
                'subtitle' => "Code: {$m->code} • Coefficient: {$m->coefficient}",
                'url' => '#',
            ]);

        // 4. Search Rooms (Salles)
        $rooms = Room::where('name', 'like', "%{$q}%")
            ->orWhere('capacity', 'like', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'title' => $r->name,
                'subtitle' => "Capacité: {$r->capacity} places • Type: " . ($r->type ?? 'Cours'),
                'url' => '#',
            ]);

        // 5. Search Exams
        $exams = Exam::with(['module', 'group', 'room'])
            ->where('is_archived', false)
            ->where(function ($query) use ($q) {
                $query->whereHas('module', function ($qm) use ($q) {
                    $qm->where('name', 'like', "%{$q}%");
                })->orWhere('type', 'like', "%{$q}%");
            })
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'title' => ($e->module?->name ?? 'Examen') . " — " . $e->type,
                'subtitle' => "Date: " . \Carbon\Carbon::parse($e->date)->format('d/m/Y') . " • Salle: " . ($e->room?->name ?? 'N/A'),
                'url' => route('admin.exam_attendance.index', $e),
            ]);

        // 6. Search Requests
        $requests = AcademicRequest::with('user')
            ->where('title', 'like', "%{$q}%")
            ->orWhere('status', 'like', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'title' => $r->title,
                'subtitle' => "Auteur: " . ($r->user?->name ?? 'Inconnu') . " • Statut: " . ($r->status ?? 'En attente'),
                'url' => '#',
            ]);

        return response()->json([
            'students' => $students,
            'professors' => $professors,
            'modules' => $modules,
            'rooms' => $rooms,
            'exams' => $exams,
            'requests' => $requests,
        ]);
    }
}
