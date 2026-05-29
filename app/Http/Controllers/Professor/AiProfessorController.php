<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LlamaAiService;
use App\Models\Reclamation;
use Illuminate\Support\Facades\Auth;

class AiProfessorController extends Controller
{
    protected LlamaAiService $aiService;

    public function __construct(LlamaAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateDraft(Request $request, Reclamation $reclamation)
    {
        // 1. Charger les données (RAG)
        $reclamation->load(['student.user', 'module', 'grade']);
        
        $student = $reclamation->student;
        $user = $student?->user;
        $module = $reclamation->module;
        $grade = $reclamation->grade;
        $motif = $reclamation->reason;

        // 2. Construire le prompt
        $systemPrompt = "Vous êtes un assistant IA pour les professeurs d'université.
Votre rôle est de rédiger un BROUILLON de réponse professionnelle, diplomatique et pédagogique à une réclamation d'étudiant concernant ses notes.
Le professeur lira et modifiera ce brouillon.
La réponse doit être adressée directement à l'étudiant (ex: 'Bonjour [Prénom]'). Ne mettez pas de balises inutiles, juste le texte de la réponse.

Contexte de la réclamation :
- Étudiant : {$user?->first_name} {$user?->last_name}
- Module : {$module?->name}
- Note actuelle (CC1): {$grade?->cc1}, (CC2): {$grade?->cc2}, (Examen): {$grade?->exam}
- Motif de l'étudiant : \"{$motif}\"

Rédigez deux courts paragraphes : un accusant réception et montrant de l'empathie, et un autre expliquant que vous allez vérifier la copie ou que la note est définitive selon le barème.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Générez le brouillon de réponse pour l'étudiant."]
        ];

        // 3. Interroger LLaMA
        $draft = $this->aiService->generateResponse($messages, 0.5);

        return response()->json([
            'draft' => $draft
        ]);
    }
}
