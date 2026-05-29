<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Notes</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #334155; }
        .header table { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h1 { font-size: 18px; color: #1e3a8a; font-weight: bold; margin: 0; }
        .title p { font-size: 10px; color: #64748b; margin-top: 5px; }
        .stats-bar { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats-card { background-color: #f1f5f9; padding: 12px; text-align: center; border: 1px solid #e2e8f0; border-radius: 8px; }
        .stats-num { font-size: 16px; font-weight: bold; color: #1e3a8a; }
        .stats-lbl { font-size: 8px; color: #64748b; text-transform: uppercase; margin-top: 3px; }
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th { background-color: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 9px; }
        table.data-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        table.data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 8px; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
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
        <h1>Procès-Verbal des Notes</h1>
        <p>Module : <strong>{{ $module->name }} ({{ $module->code }})</strong> • Filière : <strong>{{ $module->filiere?->name ?? 'N/A' }}</strong></p>
    </div>

    <table class="stats-bar">
        <tr>
            <td style="padding: 5px; width: 20%;">
                <div class="stats-card">
                    <div class="stats-num">{{ number_format($stats['avg'], 2) }}/20</div>
                    <div class="stats-lbl">Moyenne de Classe</div>
                </div>
            </td>
            <td style="padding: 5px; width: 20%;">
                <div class="stats-card">
                    <div class="stats-num">{{ number_format($stats['max'], 2) }}/20</div>
                    <div class="stats-lbl">Note Maximale</div>
                </div>
            </td>
            <td style="padding: 5px; width: 20%;">
                <div class="stats-card">
                    <div class="stats-num">{{ number_format($stats['min'], 2) }}/20</div>
                    <div class="stats-lbl">Note Minimale</div>
                </div>
            </td>
            <td style="padding: 5px; width: 20%;">
                <div class="stats-card">
                    <div class="stats-num" style="color: #059669;">{{ $stats['passed'] }}</div>
                    <div class="stats-lbl">Admis ($\ge 10$)</div>
                </div>
            </td>
            <td style="padding: 5px; width: 20%;">
                <div class="stats-card">
                    <div class="stats-num" style="color: #dc2626;">{{ $stats['failed'] }}</div>
                    <div class="stats-lbl">Ajournés ($< 10$)</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>N° Étudiant</th>
                <th>Nom Complet</th>
                <th style="text-align: right;">Contrôle 1 (40%)</th>
                <th style="text-align: right;">Contrôle 2 (40%)</th>
                <th style="text-align: right;">Examen (60%)</th>
                <th style="text-align: right;">Note Finale</th>
                <th style="text-align: center;">Résultat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $grade)
            <tr>
                <td><strong>{{ $grade->student?->student_number }}</strong></td>
                <td>{{ $grade->student?->user?->name }}</td>
                <td style="text-align: right;">{{ $grade->cc1 !== null ? number_format($grade->cc1, 2) : '—' }}</td>
                <td style="text-align: right;">{{ $grade->cc2 !== null ? number_format($grade->cc2, 2) : '—' }}</td>
                <td style="text-align: right;">{{ $grade->exam !== null ? number_format($grade->exam, 2) : '—' }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($grade->final_grade, 2) }}</td>
                <td style="text-align: center;">
                    @if($grade->final_grade >= 10)
                        <span class="badge badge-green">Admis</span>
                    @else
                        <span class="badge badge-red">Ajourné</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Signature de l'enseignant / Administration</p>
        @if($settings && $settings->signature_path)
            <img src="{{ public_path('storage/' . $settings->signature_path) }}">
        @endif
    </div>

    <div class="footer">
        {{ $settings->institution_name ?? 'Université Privée de Fès' }} — Document officiel généré automatiquement
    </div>
</body>
</html>
