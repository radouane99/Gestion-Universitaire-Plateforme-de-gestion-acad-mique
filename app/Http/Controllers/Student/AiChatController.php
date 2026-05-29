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
        $student = $user->student;

        if (!$student) {
            return response()->json(['reply' => 'Erreur: Vous n\'êtes pas reconnu comme étudiant.']);
        }

        // 1. Collecter le contexte (RAG)
        $grades = $student->grades()->with('module')->get()->map(function($g) {
            return $g->module->name . ' : ' . $g->grade . '/20';
        })->implode(', ');

        $absences = $student->absences()->count();
        $unjustifiedAbsences = $student->absences()->where('is_justified', false)->count();

        // Construire le prompt système
        $systemPrompt = "Vous êtes 'Smart UPF', l'assistant virtuel intelligent de l'Université Privée de Fès (UPF).
Votre rôle est d'aider les étudiants avec bienveillance, clarté et concision (réponses courtes, max 3-4 phrases).
Vous parlez français. Ne donnez pas d'informations que vous ne connaissez pas.
Voici les informations strictement confidentielles sur l'étudiant avec qui vous parlez actuellement (NE MENTIONNEZ PAS CES INFOS SAUF S'IL POSE UNE QUESTION EN RAPPORT) :
- Prénom : {$user->first_name}
- Nom : {$user->last_name}
- Filière : {$student->filiere->name}
- Année : {$student->academicYear->year}
- Notes actuelles : " . ($grades ?: "Aucune note saisie pour l'instant.") . "
- Absences totales : {$absences} dont {$unjustifiedAbsences} non justifiées.
Règles académiques de l'UPF : Un module est validé si la note est >= 10. Le rattrapage est possible si la note est < 10. La compensation est possible si la note est >= 7 (max 2 modules compensés par semestre). Le passage en année supérieure avec crédit est autorisé (max 2 modules).
Répondez de manière naturelle et personnalisée à la question de l'étudiant ci-dessous.";

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
