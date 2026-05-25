<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Feuille d'Émargement — {{ $exam->module->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #1a1a2e; font-size: 11px; line-height: 1.5; }
        .page { padding: 25px 30px; }

        /* Header */
        .header { border-bottom: 3px solid #1e3a8a; padding-bottom: 14px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: flex-start; }
        .logo-title { font-size: 18px; font-weight: 900; color: #1e3a8a; letter-spacing: 2px; text-transform: uppercase; }
        .logo-subtitle { font-size: 9px; color: #6b7280; letter-spacing: 1px; margin-top: 2px; }
        .doc-label { text-align: right; font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #9ca3af; }
        .doc-value { font-size: 11px; font-weight: 900; color: #be185d; }

        /* Title */
        .title-bar { background: linear-gradient(135deg, #1e3a8a, #1d4ed8); color: white; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
        .title-bar h1 { font-size: 13px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
        .title-bar .copy-note { font-size: 9px; opacity: 0.75; }

        /* Exam info grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 14px; }
        .info-cell { background: #f8faff; border: 1px solid #e5e7eb; border-radius: 5px; padding: 7px 10px; }
        .info-cell label { display: block; font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 2px; }
        .info-cell span { font-size: 12px; font-weight: 900; color: #1e3a8a; }

        /* Proctors */
        .proctors-box { background: #fffbeb; border: 1px solid #fcd34d; border-left: 4px solid #f59e0b; padding: 9px 12px; border-radius: 5px; margin-bottom: 14px; }
        .proctors-box label { font-size: 9px; text-transform: uppercase; color: #92400e; letter-spacing: 1px; display: block; margin-bottom: 4px; }
        .proctor-sig { display: inline-block; background: white; border: 1px solid #fcd34d; border-radius: 4px; padding: 3px 10px; font-size: 10px; font-weight: 700; color: #78350f; margin-right: 5px; margin-bottom: 3px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead th { background: #1e3a8a; color: white; padding: 7px 8px; text-align: left; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; font-size: 9px; }
        thead th:last-child, thead th:nth-last-child(2) { text-align: center; }
        tbody tr:nth-child(even) { background: #f8faff; }
        tbody tr { border-bottom: 1px solid #e9ecef; }
        tbody td { padding: 7px 8px; vertical-align: middle; }
        .num-col { width: 35px; color: #9ca3af; font-weight: 700; }
        .sign-col { width: 110px; text-align: center; }
        .present-col { width: 70px; text-align: center; }
        .sign-box { border: 1px solid #d1d5db; border-radius: 4px; height: 24px; background: white; }

        /* Footer */
        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 12px; display: flex; justify-content: space-between; align-items: flex-end; }
        .footer-note { font-size: 8px; color: #9ca3af; }
        .sig-zone { text-align: center; }
        .sig-zone p { font-size: 9px; color: #6b7280; }
        .sig-zone .sig-line { border-bottom: 1px solid #374151; width: 120px; height: 35px; display: inline-block; }
        .stats-box { background: #f3f4f6; border-radius: 5px; padding: 6px 12px; }
        .stats-box p { font-size: 9px; color: #6b7280; }
        .stats-box strong { font-size: 11px; color: #1e3a8a; }
    </style>
</head>
<body>
<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div>
            <div class="logo-title">🎓 Université UPF</div>
            <div class="logo-subtitle">Direction des Affaires Académiques &amp; de la Scolarité</div>
        </div>
        <div style="text-align: right;">
            <div class="doc-label">Document</div>
            <div class="doc-value">Feuille d'Émargement</div>
            <div style="font-size:8px; color:#9ca3af; margin-top:2px;">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    <!-- TITLE -->
    <div class="title-bar">
        <h1>📋 Feuille d'Émargement — {{ $exam->type }}</h1>
        <span class="copy-note">Original — À conserver par la scolarité</span>
    </div>

    <!-- EXAM INFO -->
    <div class="info-grid">
        <div class="info-cell">
            <label>Module</label>
            <span>{{ $exam->module->name }}</span>
        </div>
        <div class="info-cell">
            <label>Groupe</label>
            <span>{{ $exam->group->name }} — {{ $exam->group->filiere->name ?? '' }}</span>
        </div>
        <div class="info-cell">
            <label>Date &amp; Horaire</label>
            <span>{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} de {{ date('H:i', strtotime($exam->start_time)) }} à {{ $exam->end_time }}</span>
        </div>
        <div class="info-cell">
            <label>Salle</label>
            <span>{{ $exam->room->name ?? 'À confirmer' }}</span>
        </div>
        <div class="info-cell">
            <label>Durée</label>
            <span>{{ $exam->duration }} minutes</span>
        </div>
        <div class="info-cell">
            <label>Nombre d'étudiants</label>
            <span>{{ $convocations->count() }}</span>
        </div>
    </div>

    <!-- PROCTORS -->
    @if($exam->proctors->count())
    <div class="proctors-box">
        <label>👥 Surveillants de l'épreuve</label>
        @foreach($exam->proctors as $proctor)
            <span class="proctor-sig">{{ $proctor->user->name }}</span>
        @endforeach
    </div>
    @endif

    <!-- STUDENTS TABLE -->
    <table>
        <thead>
            <tr>
                <th class="num-col">#</th>
                <th>Nom complet</th>
                <th style="width:100px;">N° Étudiant</th>
                <th class="sign-col">Signature</th>
                <th class="present-col">Présent(e)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($convocations as $i => $conv)
            <tr>
                <td class="num-col">{{ $i + 1 }}</td>
                <td><strong>{{ $conv->student->user->name }}</strong></td>
                <td>{{ $conv->student->student_number ?? '—' }}</td>
                <td class="sign-col"><div class="sign-box"></div></td>
                <td class="present-col">
                    <div style="width:20px; height:20px; border: 1.5px solid #374151; border-radius: 4px; margin: 0 auto;"></div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #9ca3af; padding: 20px;">Aucune convocation générée pour cet examen.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <div>
            <div class="stats-box">
                <p>Total inscrits : <strong>{{ $convocations->count() }}</strong></p>
                <p>Total présents : <strong>______ / {{ $convocations->count() }}</strong></p>
                <p>Total absents : <strong>______</strong></p>
            </div>
        </div>
        <div class="sig-zone" style="margin-left: 20px;">
            <p style="margin-bottom:4px;">Signature du Surveillant responsable</p>
            <span class="sig-line"></span>
            <p style="margin-top:4px;">Nom &amp; Prénom : ___________________________</p>
        </div>
        <div class="footer-note" style="text-align:right;">
            <p>Université Privée de Fès (UPF)</p>
            <p>Direction des Affaires Académiques</p>
            <p style="color:#be185d; font-weight:700; margin-top:4px;">Document officiel — Ne pas photocopier</p>
        </div>
    </div>

</div>
</body>
</html>
