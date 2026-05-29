<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Procès-Verbal d'Examen</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #334155; line-height: 1.5; }
        .header table { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; margin-bottom: 25px; }
        .title h1 { font-size: 16px; color: #1e3a8a; font-weight: bold; margin: 0; text-transform: uppercase; }
        .title p { font-size: 10px; color: #64748b; margin-top: 5px; }
        .info-box { width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; margin-bottom: 20px; background-color: #f8fafc; }
        .info-box table { width: 100%; }
        .info-box td { padding: 4px; font-size: 10px; }
        .counts-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .counts-table th { background-color: #1e3a8a; color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 9px; }
        .counts-table td { padding: 10px; border: 1px solid #cbd5e1; text-align: center; font-size: 12px; font-weight: bold; }
        .details-section { margin-bottom: 20px; }
        .details-section h3 { font-size: 11px; color: #1e3a8a; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; }
        .details-section p { font-size: 9px; background-color: #f1f5f9; padding: 10px; border-radius: 6px; border-left: 3px solid #64748b; margin: 0; }
        .details-section .alert-p { background-color: #fef2f2; border-left: 3px solid #dc2626; color: #991b1b; }
        .footer { margin-top: 40px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .signature-area { margin-top: 50px; width: 100%; }
        .signature-area td { width: 50%; font-size: 10px; }
        .signature-area img { height: 60px; margin-top: 5px; }
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
        <h1>Procès-Verbal de Surveillance d'Examen</h1>
        <p>Document officiel établi par les surveillants affectés épreuve</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td><strong>Module :</strong> {{ $exam->module?->name }} ({{ $exam->module?->code }})</td>
                <td><strong>Type d'épreuve :</strong> {{ strtoupper($exam->type) }}</td>
            </tr>
            <tr>
                <td><strong>Date :</strong> {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</td>
                <td><strong>Horaire :</strong> {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} — {{ $exam->end_time }}</td>
            </tr>
            <tr>
                <td><strong>Salle :</strong> {{ $exam->room?->name ?? 'N/A' }}</td>
                <td><strong>Groupe :</strong> {{ $exam->group?->name }}</td>
            </tr>
        </table>
    </div>

    <table class="counts-table">
        <thead>
            <tr>
                <th>Candidats Présents</th>
                <th>Candidats Absents</th>
                <th>Candidats en Retard</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #047857;">{{ $pv->presents_count }}</td>
                <td style="color: #b91c1c;">{{ $pv->absents_count }}</td>
                <td style="color: #b45309;">{{ $pv->retards_count }}</td>
            </tr>
        </tbody>
    </table>

    <div class="details-section">
        <h3>1. Constatations de Fraude</h3>
        @if($pv->fraude_detected)
            <p class="alert-p">
                <strong>⚠️ OUI - Fraude signalée :</strong><br>
                {{ $pv->fraude_details }}
            </p>
        @else
            <p>Aucun comportement frauduleux ou tentative de tricherie n'a été observé durant l'épreuve. ✅</p>
        @endif
    </div>

    <div class="details-section">
        <h3>2. Incidents observés durant l'épreuve</h3>
        <p>
            {{ $pv->incidents ?: "Aucun incident ou perturbation n'a été signalé durant l'épreuve." }}
        </p>
    </div>

    <div class="details-section">
        <h3>3. Remarques administratives</h3>
        <p>
            {{ $pv->remarques ?: "Aucune remarque particulière." }}
        </p>
    </div>

    <table class="signature-area">
        <tr>
            <td style="text-align: left; vertical-align: top;">
                <strong>Surveillant(s) affecté(s) :</strong><br>
                <span style="font-size: 9px; color: #64748b;">
                    @foreach($exam->proctors as $p)
                        • {{ $p->user?->name }}<br>
                    @endforeach
                    @if($exam->proctors->isEmpty())
                        Surveillant non assigné
                    @endif
                </span>
                <div style="margin-top: 15px; border-bottom: 1px dashed #cbd5e1; width: 150px; height: 40px;"></div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <strong>Cachet de l'établissement</strong><br>
                @if($settings && $settings->signature_path)
                    <img src="{{ public_path('storage/' . $settings->signature_path) }}">
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ $settings->institution_name ?? 'Université Privée de Fès' }} — Procès-Verbal officiel d'Examen
    </div>
</body>
</html>
