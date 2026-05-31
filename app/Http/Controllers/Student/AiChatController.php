<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LlamaAiService;
use Illuminate\Support\Facades\Auth;

class AiChatController extends Controller
{
    protected LlamaAiService $aiService;

    public function __construct(LlamaAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        
        if ($user->isStudent() && $user->student) {
            $student = $user->student;
            // 1. Collecter le contexte (RAG) pour Étudiant
            $grades = $student->grades()->with('module')->get()->map(function($g) {
                return $g->module->name . ' : ' . $g->grade . '/20';
            })->implode(', ');
    
            $absences = $student->absences()->count();
            $unjustifiedAbsences = $student->absences()->where('is_justified', false)->count();
            $filiereName = $student->filiere ? $student->filiere->name : 'Non assignée';
            $academicYear = $student->academicYear ? $student->academicYear->year : 'Non assignée';
    
            // Construire le prompt système Étudiant
            $systemPrompt = "Vous êtes 'Smart UPF', l'assistant virtuel intelligent de l'Université Privée de Fès (UPF).
Votre rôle est d'aider les étudiants avec bienveillance, clarté et concision (réponses courtes, max 3-4 phrases).
Vous parlez français. Ne donnez pas d'informations que vous ne connaissez pas.
Voici les informations strictement confidentielles sur l'étudiant avec qui vous parlez actuellement (NE MENTIONNEZ PAS CES INFOS SAUF S'IL POSE UNE QUESTION EN RAPPORT) :
- Nom : {$user->name}
- Filière : {$filiereName}
- Année : {$academicYear}
- Notes actuelles : " . ($grades ?: "Aucune note saisie pour l'instant.") . "
- Absences totales : {$absences} dont {$unjustifiedAbsences} non justifiées.
Règles académiques de l'UPF : Un module est validé si la note est >= 10. Le rattrapage est possible si la note est < 10. La compensation est possible si la note est >= 7 (max 2 modules compensés par semestre). Le passage en année supérieure avec crédit est autorisé (max 2 modules).
Répondez de manière naturelle et personnalisée à la question de l'étudiant ci-dessous.";

        } elseif ($user->isProfessor() && $user->professor) {
            $professor = $user->professor;
            $systemPrompt = "Vous êtes 'Smart UPF', l'assistant virtuel intelligent de l'Université Privée de Fès (UPF).
Votre rôle est d'assister les professeurs. Soyez professionnel, utile et concis (réponses courtes, max 3-4 phrases).
Vous parlez français. Ne donnez pas d'informations que vous ne connaissez pas.
Voici les informations sur le professeur avec qui vous parlez actuellement :
- Nom : {$user->name}
- Spécialité : " . ($professor->specialty ?? 'Non précisée') . "
Le professeur peut utiliser la plateforme pour gérer son cahier de textes, marquer les absences, saisir les notes, et gérer ses rendez-vous et messages avec les étudiants.
Aidez-le à préparer ses cours, rédiger des messages professionnels pour les étudiants, trouver des idées d'exercices, ou répondre à des questions pédagogiques.
Répondez de manière naturelle et personnalisée à la question du professeur ci-dessous.";

        } elseif ($user->isAdmin()) {
            $systemPrompt = "Vous êtes 'Smart UPF', l'assistant virtuel intelligent de l'Université Privée de Fès (UPF).
Votre rôle est d'assister les administrateurs de la scolarité. Soyez professionnel, précis et concis (réponses courtes, max 3-4 phrases).
Vous parlez français. Ne donnez pas d'informations que vous ne connaissez pas.
L'utilisateur actuel est un Administrateur ({$user->name}).
Il a tous les droits sur la plateforme : gérer les étudiants, les professeurs, les absences, les notes, les plannings (emplois du temps) et traiter les réclamations.
Aidez-le dans ses tâches de gestion administrative, répondez à ses questions ou aidez-le à rédiger des documents administratifs (emails officiels, convocations, rapports, etc.).
Répondez de manière naturelle et personnalisée à la question de l'administrateur ci-dessous.";

        } else {
            $systemPrompt = "Vous êtes 'Smart UPF', l'assistant virtuel de l'Université Privée de Fès (UPF).
Votre rôle est d'aider l'utilisateur avec bienveillance, clarté et concision (réponses courtes, max 3-4 phrases).
Vous parlez français. Ne donnez pas d'informations que vous ne connaissez pas.";
        }

        // Préparer les messages
        // (On pourrait gérer un historique de conversation en session, mais pour simplifier on garde le contexte immédiat)
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $request->message]
        ];

        // 2. Interroger LLaMA
        $reply = $this->aiService->generateResponse($messages, 0.4);

        return response()->json([
            'reply' => $reply
        ]);
    }
}
