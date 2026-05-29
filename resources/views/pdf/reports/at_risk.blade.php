<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étudiants à Risque</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #334155; }
        .header table { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h1 { font-size: 18px; color: #991b1b; font-weight: bold; margin: 0; }
        .title p { font-size: 10px; color: #64748b; margin-top: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #991b1b; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 9px; }
        table.data-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        table.data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .footer { margin-top: 40px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 30%;">
                    @if($settings && $settings->logo_path)
                        <img src="{{ public_path('storage/' . $settings->logo_path) }}" style="height: 50px;">
                    @else
                        <span style="font-size: 20px; font-weight: bold; color: #1e3a8a;">UPF</span>
                    @endif
                </td>
                <td style="width: 70%; text-align: right;">
                    <h2 style="font-size: 14px; color: #1e3a8a; margin: 0;">{{ $settings->institution_name ?? 'Université Privée de Fès' }}</h2>
                    <p style="font-size: 8px; color: #64748b; margin: 3px 0 0 0;">
                        {{ $settings->address ?? 'Fès, Maroc' }}<br>
                        Tél: {{ $settings->phone ?? 'N/A' }} | Email: {{ $settings->official_email ?? 'N/A' }}<br>
                        <strong>Année Académique : {{ $settings->academic_year ?? '2025-2026' }}</strong>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">
        <h1>Rapport d'Alerte : Étudiants à Risque</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>N° Étudiant</th>
                <th>Nom Complet</th>
                <th>Groupe / Filière</th>
                <th style="text-align: right;">Moyenne Active</th>
                <th style="text-align: right;">Heures d'Absence (NJ)</th>
                <th>Statut de Risque</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atRiskStudents as $item)
            <tr>
                <td><strong>{{ $item->student->student_number }}</strong></td>
                <td>{{ $item->student->user?->name }}</td>
                <td>{{ $item->student->group?->name }} ({{ $item->student->group?->filiere?->name }})</td>
                <td style="text-align: right; font-weight: bold;">
                    {{ $item->moyenne !== null ? number_format($item->moyenne, 2) . '/20' : '—' }}
                </td>
                <td style="text-align: right; font-weight: bold; color: #dc2626;">
                    {{ number_format($item->absences, 1) }}h
                </td>
                <td>
                    <strong class="{{ $item->color }}">{{ $item->risk }}</strong>
                </td>
            </tr>
            @endforeach
            @if($atRiskStudents->isEmpty())
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; color: #64748b; font-style: italic;">
                    Aucun étudiant à risque détecté par l'algorithme prédictif. ✅
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        {{ $settings->institution_name ?? 'Université Privée de Fès' }} — Document officiel généré automatiquement
    </div>
</body>
</html>
