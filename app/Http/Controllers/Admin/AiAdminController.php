<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LlamaAiService;
use App\Models\Student;

class AiAdminController extends Controller
{
    protected LlamaAiService $aiService;

    public function __construct(LlamaAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateReport(Student $student)
    {
        // 1. Charger les données (RAG)
        $student->load(['user', 'filiere', 'group', 'grades.module', 'absences']);
        
        $user = $student->user;
        $filiere = $student->filiere;
        
        $grades = $student->grades->map(function($g) {
            return $g->module->name . ' (' . $g->final_grade . '/20)';
        })->implode(', ');

        $totalAbsences = $student->absences->count();
        $unjustifiedAbsences = $student->absences->where('is_justified', false)->count();

        // 2. Construire le prompt
        $systemPrompt = "Vous êtes le 'Conseiller Pédagogique IA' d'une université (UPF).
Votre rôle est d'analyser le dossier académique d'un étudiant et de rédiger un 'Bilan Pédagogique' professionnel à destination de l'administration.
Le bilan doit être structuré, objectif, et se terminer par une ou deux recommandations concrètes (ex: 'Convoquer l'étudiant', 'Féliciter pour ses résultats').

Dossier de l'étudiant :
- Nom : {$user->first_name} {$user->last_name}
- Filière : {$filiere?->name}
- Notes actuelles : " . ($grades ?: "Aucune note saisie.") . "
- Absences : {$totalAbsences} au total (dont {$unjustifiedAbsences} NON justifiées).

Rédigez le bilan en 3 courtes parties (Points forts, Points de vigilance, Recommandations). Utilisez le HTML basique (<b>, <ul>, <li>) pour formater la réponse.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Génère le bilan pédagogique de l'étudiant."]
        ];

        // 3. Interroger LLaMA
        $report = $this->aiService->generateResponse($messages, 0.4);

        return response()->json([
            'report' => $report
        ]);
    }
}
