<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning des Examens</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #334155; }
        .header table { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h1 { font-size: 18px; color: #1e3a8a; font-weight: bold; margin: 0; }
        .title p { font-size: 10px; color: #64748b; margin-top: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 9px; }
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
        <h1>Calendrier Général et Affectation des Examens</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Horaire</th>
                <th>Module / Code</th>
                <th>Groupe / Filière</th>
                <th>Type</th>
                <th>Salle affectée</th>
                <th>Surveillants affectés</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
            <tr>
                <td><strong>{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} — {{ $exam->end_time }}</td>
                <td><strong>{{ $exam->module?->name }}</strong><br><span style="font-size: 8px; color: #64748b;">{{ $exam->module?->code }}</span></td>
                <td>{{ $exam->group?->name }} ({{ $exam->group?->filiere?->name }})</td>
                <td><span style="text-transform: uppercase;">{{ $exam->type }}</span></td>
                <td><strong>{{ $exam->room?->name ?? 'N/A' }}</strong></td>
                <td>
                    @foreach($exam->proctors as $p)
                        {{ $p->user?->name }}<br>
                    @endforeach
                    @if($exam->proctors->isEmpty())
                        <span style="color: #dc2626; font-style: italic;">Non affectés</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $settings->institution_name ?? 'Université Privée de Fès' }} — Document officiel généré automatiquement
    </div>
</body>
</html>
