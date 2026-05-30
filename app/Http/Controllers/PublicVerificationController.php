<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    /**
     * Vérifie publiquement l'authenticité d'une attestation scannée via QR Code.
     */
    public function verifyDocument(string $token)
    {
        // Recherche de l'étudiant via son hash unique
        $student = Student::where('registration_status', 'approved')
            ->get()
            ->first(function ($s) use ($token) {
                return $s->document_token === $token;
            });

        if (!$student) {
            abort(404, "Ce document académique est invalide, a été révoqué ou n'existe pas dans nos registres.");
        }

        $gpa = $student->getYearlyGpa();
        
        $mention = match (true) {
            $gpa >= 16.0 => 'Très Bien',
            $gpa >= 14.0 => 'Bien',
            $gpa >= 12.0 => 'Assez Bien',
            $gpa >= 10.0 => 'Passable',
            default      => 'Passable',
        };

        return view('public.verify_document', compact('student', 'gpa', 'mention'));
    }
}
