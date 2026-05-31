<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Internship;
use App\Models\Professor;
use App\Models\ActivityLog;

class InternshipController extends Controller
{
    public function index()
    {
        $internships = Internship::with(['student.user', 'student.group', 'academicTutor.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $professors = Professor::with('user')->get();

        return view('admin.internships.index', compact('internships', 'professors'));
    }

    public function approve(Request $request, Internship $internship)
    {
        $validated = $request->validate([
            'academic_tutor_id' => 'required|exists:professors,id',
        ]);

        $internship->update([
            'academic_tutor_id' => $validated['academic_tutor_id'],
            'status' => 'active',
        ]);

        // Notify student of approval
        $internship->student->user?->notify(new \App\Notifications\AcademicNotification(
            "🟢 Votre demande de stage a été validée par la direction !",
            'success',
            route('student.internships.index')
        ));

        // Notify professor of tutoring assignment
        $tutor = Professor::find($validated['academic_tutor_id']);
        $tutor->user?->notify(new \App\Notifications\AcademicNotification(
            "🏛️ Vous avez été désigné comme tuteur académique pour le stage de {$internship->student->user->name}",
            'info',
            route('professor.internships.index')
        ));

        ActivityLog::log('approved', 'Internship', "Stage validé pour l'étudiant #{$internship->student_id} avec tuteur académique ID {$validated['academic_tutor_id']}.");

        return back()->with('success', 'La fiche de stage a été validée et le tuteur académique a été assigné.');
    }

    public function reject(Request $request, Internship $internship)
    {
        $internship->update([
            'status' => 'rejected',
        ]);

        // Notify student of rejection
        $internship->student->user?->notify(new \App\Notifications\AcademicNotification(
            "🔴 Votre demande de stage a été refusée. Veuillez contacter l'administration.",
            'danger',
            route('student.internships.index')
        ));

        ActivityLog::log('rejected', 'Internship', "Stage rejeté pour l'étudiant #{$internship->student_id}.");

        return back()->with('success', 'La demande de stage a été rejetée.');
    }
}
