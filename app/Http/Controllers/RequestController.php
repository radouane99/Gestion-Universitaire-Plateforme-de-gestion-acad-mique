<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index()
    {
        $requests = \App\Models\Request::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.requests.index', compact('requests'));
    }

    public function createProfessorRequest()
    {
        $requests = \App\Models\Request::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('professor.requests.create', compact('requests'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'reason' => 'nullable|string',
            'destination' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'mission_reason' => 'nullable|string',
        ]);

        $data = [];
        if ($validated['type'] == 'Ordre de Mission') {
            $data = [
                'destination' => $validated['destination'] ?? '',
                'start_date' => $validated['start_date'] ?? '',
                'end_date' => $validated['end_date'] ?? '',
                'mission_reason' => $validated['mission_reason'] ?? '',
            ];
        }

        $newReq = \App\Models\Request::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'data' => $data,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Request',
            'description' => "Demande de document '{$newReq->type}' soumise.",
            'ip_address' => $request->ip()
        ]);

        $admins = \App\Models\User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\AcademicNotification(
                "Nouvelle demande '{$newReq->type}' soumise par " . Auth::user()->name,
                'info',
                route('admin.requests.index')
            ));
        }

        if (Auth::user()->isProfessor()) {
            return redirect()->route('professor.requests.create')->with('success', 'Demande administrative soumise avec succès.');
        }

        return redirect()->route('student.requests.create')->with('success', 'Demande administrative soumise avec succès.');
    }

    public function update(\Illuminate\Http\Request $httpRequest, \App\Models\Request $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string', // Support for rejection reason
        ]);

        $request->update([
            'status' => $validated['status'],
            'reason' => $validated['status'] == 'rejected' ? ($validated['reason'] ?? 'Refusé par l\'administration') : null
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $validated['status'] == 'approved' ? 'approved' : 'rejected',
            'model_type' => 'Request',
            'description' => "Demande '{$request->type}' de " . ($request->user->name ?? '') . " " . ($validated['status'] == 'approved' ? 'approuvée' : 'rejetée'),
            'ip_address' => $httpRequest->ip()
        ]);

        // Notify User with direct PDF link if approved
        $notifMessage = $validated['status'] === 'approved'
            ? "✅ Votre demande [{$request->type}] a été APPROUVÉE. Votre document est prêt à être téléchargé."
            : "❌ Votre demande [{$request->type}] a été REFUSÉE. Motif : " . ($validated['reason'] ?? 'Non précisé');

        $notifUrl = $validated['status'] === 'approved'
            ? route('admin.requests.show', ['adminRequest' => $request->id])
            : route('dashboard');

        $request->user->notify(new \App\Notifications\AcademicNotification(
            $notifMessage,
            $validated['status'] === 'approved' ? 'success' : 'error',
            $notifUrl
        ));

        return back()->with('success', 'Le statut de la demande a été mis à jour et l\'utilisateur a été notifié.');
    }

    public function updateStatusAjax(\Illuminate\Http\Request $httpRequest, \App\Models\Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $httpRequest->validate([
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $request->update([
            'status' => $validated['status'],
            'reason' => $validated['status'] == 'rejected' ? ($validated['reason'] ?? 'Refusé par l\'administration') : null
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'status_updated',
            'model_type' => 'Request',
            'description' => "Le statut de la demande '{$request->type}' de " . ($request->user->name ?? '') . " a été mis à jour via Kanban ({$validated['status']})",
            'ip_address' => $httpRequest->ip()
        ]);

        // Notifications
        if ($validated['status'] !== 'pending') {
            $notifMessage = $validated['status'] === 'approved'
                ? "✅ Votre demande [{$request->type}] a été APPROUVÉE. Votre document est prêt à être téléchargé."
                : "❌ Votre demande [{$request->type}] a été REFUSÉE. Motif : " . ($validated['reason'] ?? 'Non précisé');

            $notifUrl = $validated['status'] === 'approved'
                ? route('admin.requests.show', ['adminRequest' => $request->id])
                : route('dashboard');

            $request->user->notify(new \App\Notifications\AcademicNotification(
                $notifMessage,
                $validated['status'] === 'approved' ? 'success' : 'error',
                $notifUrl
            ));
        }

        return response()->json(['success' => true]);
    }

    public function show(\App\Models\Request $adminRequest)
    {
        if ($adminRequest->status !== 'approved') return abort(403);

        if (!Auth::user()->isAdmin() && Auth::id() !== $adminRequest->user_id) {
            return abort(403, 'Unauthorized action.');
        }
        
        $request = $adminRequest->load('user');
        
        if ($request->type == 'Transcript' || $request->type == 'Relevé de Notes') {
            $student = $request->user->student;
            $gradesBySemester = collect();
            $yearlyGPA = 0;
            
            if ($student) {
                $grades = \App\Models\Grade::where('student_id', $student->id)->with(['module.semester'])->get();
                $gradesBySemester = $grades->groupBy(function($g) {
                    return $g->module && $g->module->semester ? $g->module->semester->name : 'Autres Modules';
                })->sortKeys();
                
                $totalGPA = 0;
                $validSemestersCount = 0;
                foreach($gradesBySemester as $sem => $semGrades) {
                    $count = $semGrades->whereNotNull('final_grade')->count();
                    if ($count > 0) {
                        $totalGPA += $semGrades->whereNotNull('final_grade')->avg('final_grade');
                        $validSemestersCount++;
                    }
                }
                $yearlyGPA = $validSemestersCount > 0 ? $totalGPA / $validSemestersCount : 0;
            }
            return view('documents.transcript', compact('request', 'gradesBySemester', 'student', 'yearlyGPA'));
        }
        
        if ($request->type == 'Attestation de Travail') {
            return view('documents.work_certificate', compact('request'));
        }

        if ($request->type == 'Ordre de Mission') {
            return view('documents.mission_order', compact('request'));
        }
        
        if ($request->type == 'Convention de Stage') {
            return view('documents.internship_agreement', compact('request'));
        }
        
        return view('documents.certificate', compact('request'));
    }
}
