<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste d'Affichage - {{ $exam->module->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #003399; padding-bottom: 10px; margin-bottom: 20px; }
        .univ-name { font-size: 18px; font-weight: bold; color: #003399; text-transform: uppercase; }
        .doc-title { font-size: 22px; font-weight: bold; margin: 15px 0; letter-spacing: 1px; }
        .exam-info { width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #f8fafc; }
        .exam-info td { padding: 10px; border: 1px solid #e2e8f0; }
        .exam-info strong { color: #003399; }
        
        .students-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .students-table th { background-color: #003399; color: white; padding: 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .students-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
        .students-table tr:nth-child(even) { background-color: #f8fafc; }
        
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 10px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="univ-name">{{ App\Models\Setting::first()->institution_name ?? 'UNIVERSITÉ PRIVÉE DE FÈS' }}</div>
        <div class="doc-title">LISTE D'AFFICHAGE DES EXAMENS</div>
    </div>

    <table class="exam-info">
        <tr>
            <td><strong>Session :</strong> {{ $exam->examSession->name ?? 'N/A' }}</td>
            <td><strong>Date :</strong> {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</td>
            <td><strong>Horaire :</strong> {{ $exam->start_time }} ({{ $exam->duration }} min)</td>
        </tr>
        <tr>
            <td><strong>Filière / Groupe :</strong> {{ $exam->group->filiere->name }} - {{ $exam->group->name }}</td>
            <td><strong>Module :</strong> {{ $exam->module->name }}</td>
            <td><strong>Salle :</strong> {{ $exam->room->name }}</td>
        </tr>
        <tr>
            <td colspan="3">
                <strong>Surveillants :</strong> 
                @forelse($exam->proctors as $proctor)
                    {{ $proctor->user->name }}{{ !$loop->last ? ', ' : '' }}
                @empty
                    Aucun
                @endforelse
            </td>
        </tr>
    </table>

    <table class="students-table">
        <thead>
            <tr>
                <th style="width: 15%">Place N°</th>
                <th style="width: 25%">Matricule</th>
                <th style="width: 45%">Nom & Prénom</th>
                <th style="width: 15%">Émargement</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exam->convocations->sortBy('student.user.name') as $convocation)
                <tr>
                    <td style="font-weight: bold; font-size: 14px;">{{ str_replace('Place ', '', $convocation->seat_number) }}</td>
                    <td>{{ $convocation->student->student_number }}</td>
                    <td>{{ strtoupper($convocation->student->user->name) }}</td>
                    <td></td> <!-- Case vide pour signature -->
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Aucun étudiant assigné</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} par le système de gestion des examens.
    </div>

</body>
</html>
