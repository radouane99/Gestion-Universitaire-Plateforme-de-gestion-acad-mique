<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste de Rattrapage — {{ $session->name ?? 'Session' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'DejaVu Sans', Arial, sans-serif; }
        body { font-size: 10px; color: #1e293b; background: #fff; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 3px solid #1e3a6e; }
        .header h1 { font-size: 20px; font-weight: 900; color: #1e3a6e; }
        .header p { font-size: 11px; color: #64748b; margin-top: 5px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 9px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead tr { background: #1e3a6e; color: white; }
        thead th { padding: 8px 6px; text-align: left; font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #eff6ff; }
        tbody td { padding: 7px 6px; border-bottom: 1px solid #e2e8f0; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: 900; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .stat-bar { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-item { flex: 1; text-align: center; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .stat-num { font-size: 18px; font-weight: 900; color: #1e3a6e; }
        .stat-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Liste de Rattrapage — {{ $session->name ?? 'Session' }}</h1>
        <p>{{ $session->type ?? '' }} • Année {{ $session->start_date?->format('Y') }} — Générée le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="stat-bar">
        <div class="stat-item">
            <div class="stat-num">{{ $eligibilities->count() }}</div>
            <div class="stat-label">Total Étudiants</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">{{ $eligibilities->where('reason', 'exam_absence_justified')->count() }}</div>
            <div class="stat-label">Absences Justifiées</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">{{ $eligibilities->where('reason', 'low_grade')->count() }}</div>
            <div class="stat-label">Notes Insuffisantes</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>N° Étudiant</th>
                <th>Nom et Prénom</th>
                <th>Groupe</th>
                <th>Filière</th>
                <th>Module</th>
                <th>Raison</th>
                <th>Décision</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eligibilities as $i => $e)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $e->student?->student_number }}</strong></td>
                <td>{{ $e->student?->user?->name }}</td>
                <td>{{ $e->student?->group?->name }}</td>
                <td>{{ $e->student?->group?->filiere?->name }}</td>
                <td>{{ $e->exam?->module?->name }}</td>
                <td>
                    @if($e->reason === 'low_grade')
                        <span class="badge badge-amber">Note Faible</span>
                    @else
                        <span class="badge badge-blue">Abs. Justifiée</span>
                    @endif
                </td>
                <td><span class="badge badge-green">Accordé</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Plateforme de Gestion Académique — Document officiel généré automatiquement
    </div>
</body>
</html>
