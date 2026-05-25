<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convocation d'Examen - {{ $exam->module->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { width: 150px; margin-bottom: 10px; }
        .title { color: #1e3a8a; text-transform: uppercase; letter-spacing: 2px; margin: 0; }
        .subtitle { color: #6b7280; font-size: 14px; margin-top: 5px; }
        .details-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 30px; background-color: #f9fafb; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table th { text-align: left; padding: 8px 0; color: #4b5563; font-weight: bold; width: 30%; }
        .details-table td { padding: 8px 0; font-weight: bold; color: #111827; }
        .section-title { color: #1e3a8a; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 15px; }
        .proctors-list { list-style-type: none; padding-left: 0; }
        .proctors-list li { padding: 5px 0; border-bottom: 1px dashed #e5e7eb; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .important-notice { background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-top: 30px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Université UPF</h1>
        <p class="subtitle">Convocation Officielle d'Examen</p>
    </div>

    <div class="details-box">
        <h2 class="section-title">Détails de l'Examen</h2>
        <table class="details-table">
            <tr>
                <th>Module :</th>
                <td>{{ $exam->module->name }}</td>
            </tr>
            <tr>
                <th>Type d'Évaluation :</th>
                <td>{{ $exam->type }}</td>
            </tr>
            <tr>
                <th>Groupe / Filière :</th>
                <td>{{ $exam->group->name }} ({{ $exam->group->filiere->name ?? 'N/A' }})</td>
            </tr>
            <tr>
                <th>Date :</th>
                <td>{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Heure :</th>
                <td>{{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }}</td>
            </tr>
            <tr>
                <th>Durée :</th>
                <td>{{ $exam->duration }} minutes</td>
            </tr>
            <tr>
                <th>Salle :</th>
                <td>{{ $exam->room?->name ?? 'À définir' }}</td>
            </tr>
        </table>
    </div>

    @if($exam->proctors->count() > 0)
    <div>
        <h2 class="section-title">Surveillants Assignés</h2>
        <ul class="proctors-list">
            @foreach($exam->proctors as $proctor)
                <li>Dr. {{ $proctor->user->name }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="important-notice">
        <strong>Important :</strong> La présentation de la carte d'étudiant est obligatoire pour accéder à la salle d'examen. Les étudiants doivent se présenter 15 minutes avant le début de l'épreuve.
    </div>

    <div class="footer">
        Document généré informatiquement le {{ now()->format('d/m/Y à H:i') }}.<br>
        Direction des Affaires Académiques - UPF
    </div>
</body>
</html>
