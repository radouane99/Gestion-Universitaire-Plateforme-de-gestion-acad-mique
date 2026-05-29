<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'Absences</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #334155; }
        .header table { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h1 { font-size: 18px; color: #1e3a8a; font-weight: bold; margin: 0; }
        .title p { font-size: 10px; color: #64748b; margin-top: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        table.data-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        table.data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 8px; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .badge-orange { background-color: #ffedd5; color: #9a3412; }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .footer { margin-top: 40px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .signature { margin-top: 40px; text-align: right; }
        .signature img { height: 65px; margin-top: 5px; }
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
        <h1>Rapport Global des Absences</h1>
        <p>Groupe : <strong>{{ $groupName }}</strong> • Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>N° Étudiant</th>
                <th>Nom Complet</th>
                <th>Groupe / Filière</th>
                <th style="text-align: right;">Heures NJ</th>
                <th style="text-align: right;">Heures Justifiées</th>
                <th style="text-align: right;">Total Heures</th>
                <th style="text-align: center;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td><strong>{{ $student->student_number }}</strong></td>
                <td>{{ $student->user?->name }}</td>
                <td>{{ $student->group?->name }} ({{ $student->group?->filiere?->name }})</td>
                <td style="text-align: right; color: #b91c1c; font-weight: bold;">{{ number_format($student->absence_score, 1) }}h</td>
                <td style="text-align: right; color: #047857;">{{ number_format($student->justified_hours, 1) }}h</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($student->total_absence_hours, 1) }}h</td>
                <td style="text-align: center;">
                    @php $status = $student->discipline_status; @endphp
                    @if($status === 'conseil_discipline')
                        <span class="badge badge-red">Conseil de discipline</span>
                    @elseif($status === 'a_surveiller')
                        <span class="badge badge-orange">À Surveiller</span>
                    @else
                        <span class="badge badge-green">Normal</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Cachet et signature de l'administration</p>
        @if($settings && $settings->signature_path)
            <img src="{{ public_path('storage/' . $settings->signature_path) }}">
        @endif
    </div>

    <div class="footer">
        {{ $settings->institution_name ?? 'Université Privée de Fès' }} — Document officiel généré automatiquement
    </div>
</body>
</html>
