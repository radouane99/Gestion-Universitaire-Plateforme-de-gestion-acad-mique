<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Convocation;
use Illuminate\Support\Facades\Auth;

class ConvocationController extends Controller
{
    /**
     * Show all student convocations
     */
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(403);
        }

        $convocations = Convocation::where('student_id', $student->id)
            ->with(['exam.module', 'exam.room', 'exam.group', 'exam.proctors.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Split into upcoming and past
        $upcoming = $convocations->filter(fn($c) => \Carbon\Carbon::parse($c->exam->date)->isFuture())->values();
        $past     = $convocations->filter(fn($c) => \Carbon\Carbon::parse($c->exam->date)->isPast())->values();

        return view('student.convocations.index', compact('upcoming', 'past'));
    }

    /**
     * Download a single convocation as PDF
     */
    public function download(Convocation $convocation)
    {
        $student = Auth::user()->student;

        // Security: only the owner can download
        if ($convocation->student_id !== $student->id) {
            abort(403, 'Accès non autorisé.');
        }

        $convocation->load(['exam.module', 'exam.room', 'exam.group.filiere', 'exam.proctors.user', 'student.user', 'student.group.filiere']);

        // Mark as downloaded
        if ($convocation->status === 'pending' || $convocation->status === 'sent') {
            $convocation->update(['status' => 'downloaded']);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('convocations.pdf', compact('convocation'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('convocation_' . $convocation->reference . '.pdf');
    }
}
