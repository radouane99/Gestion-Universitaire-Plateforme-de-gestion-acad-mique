<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convocation aux Examens — {{ $convocation->reference }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.45;
        }

        /* ======================== PAGE WRAPPER ======================== */
        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            overflow: hidden;
            padding: 8mm 12mm 8mm 12mm;
            position: relative;
            background: #ffffff;
        }

        /* ======================== DOUBLE GOLD BORDER ======================== */
        .doc-border {
            border: 7px double #c9a227;
            padding: 10px 14px 8px 14px;
            height: 270mm;
            position: relative;
            overflow: hidden;
        }

        /* ======================== WATERMARK ======================== */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 70px;
            color: rgba(0, 56, 147, 0.035);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
            letter-spacing: 2px;
        }

        /* ======================== HEADER ======================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #c9a227;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .hdr-left {
            width: 32%;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            color: #003893;
            line-height: 1.5;
        }
        .hdr-center {
            width: 36%;
            text-align: center;
        }
        .hdr-center img {
            height: 52px;
            display: block;
            margin: 0 auto 2px auto;
        }
        .hdr-center-title {
            font-size: 8px;
            font-weight: bold;
            color: #003893;
            letter-spacing: 0.3px;
            line-height: 1.3;
        }
        .hdr-right {
            width: 32%;
            text-align: right;
            font-size: 7.5px;
            font-weight: bold;
            color: #9b1d6e;
            line-height: 1.5;
            direction: rtl;
        }

        /* Sub-line under institution names */
        .sub-divider {
            height: 2px;
            background: linear-gradient(to right, #003893, #c9a227, #9b1d6e);
            border-radius: 2px;
            margin-bottom: 8px;
        }

        /* ======================== TITLE ======================== */
        .conv-title {
            text-align: center;
            margin: 6px 0 8px 0;
        }
        .conv-title-main {
            font-size: 18px;
            font-weight: bold;
            color: #003893;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #9b1d6e;
            display: inline-block;
            padding-bottom: 3px;
        }
        .conv-title-session {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            margin-top: 3px;
        }

        /* ======================== STUDENT INFO ======================== */
        .student-table {
            width: 100%;
            margin: 6px 0 10px 0;
            border-collapse: collapse;
            border: 1px solid rgba(201,162,39,0.3);
            background: rgba(201,162,39,0.02);
            position: relative;
            z-index: 1;
        }
        .student-table td {
            padding: 5px 10px;
            font-size: 11px;
            border-bottom: 1px solid rgba(201,162,39,0.15);
        }
        .student-table tr:last-child td {
            border-bottom: none;
        }
        .s-label {
            font-weight: bold;
            width: 25%;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .s-value {
            font-weight: bold;
            color: #0f172a;
        }
        .s-value.blue {
            color: #003893;
        }

        /* ======================== NOTICE ======================== */
        .notice {
            font-size: 11.5px;
            color: #334155;
            margin-bottom: 8px;
            font-style: italic;
            position: relative;
            z-index: 1;
        }

        /* ======================== EXAMS TABLE ======================== */
        .exams-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
            position: relative;
            z-index: 1;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .exams-table thead tr {
            background-color: #003893;
            color: #ffffff;
        }
        .exams-table th {
            border: 1px solid rgba(201,162,39,0.3);
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        .exams-table tbody tr {
            background: #ffffff;
        }
        .exams-table tbody tr:nth-child(even) {
            background: rgba(0,56,147,0.02);
        }
        .exams-table td {
            border: 1px solid rgba(0,56,147,0.1);
            padding: 6px 5px;
            text-align: center;
            color: #1e293b;
        }
        .exams-table td.text-left {
            text-align: left;
            padding-left: 8px;
        }
        .exams-table td.bold {
            font-weight: bold;
            color: #0f172a;
        }

        /* ======================== RULES ======================== */
        .rules-section {
            margin-bottom: 8px;
            border-left: 3px solid #c9a227;
            background: rgba(201,162,39,0.03);
            padding: 6px 10px;
            position: relative;
            z-index: 1;
        }
        .rules-title {
            font-size: 11px;
            font-weight: bold;
            color: #9b1d6e;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .rules-text {
            font-size: 7.5px;
            line-height: 1.45;
            color: #334155;
            text-align: justify;
        }
        .rules-text ul {
            list-style: none;
        }
        .rules-text li {
            margin-bottom: 2px;
            padding-left: 8px;
            position: relative;
        }
        .rules-text li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #c9a227;
            font-weight: bold;
        }

        /* ======================== SIGNATURE & CACHET ======================== */
        .footer-section {
            position: absolute;
            bottom: 10px;
            left: 14px;
            right: 14px;
        }

        .footer-sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .footer-sig-table td {
            vertical-align: bottom;
            padding: 0;
        }
        .footer-left-cell {
            width: 45%;
            text-align: left;
            vertical-align: bottom;
        }
        .footer-left-note {
            font-size: 7px;
            color: #64748b;
            font-weight: bold;
        }
        .footer-left-note p {
            margin-bottom: 2px;
        }
        .footer-left-note img {
            border: 1px solid #c9a227;
            padding: 3px;
            background: white;
            border-radius: 4px;
            display: block;
        }
        .footer-right-cell {
            width: 55%;
            text-align: right;
            vertical-align: top;
        }
        .sig-name-title {
            font-size: 10px;
            font-weight: bold;
            color: #003893;
            margin-bottom: 4px;
            text-decoration: underline;
        }

        /* Stamp circle */
        .stamp-circle {
            width: 95px;
            height: 95px;
            border: 2px double #003893;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background: rgba(0,56,147,0.02);
        }
        .stamp-inner {
            width: 83px;
            height: 83px;
            border: 1px solid #003893;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            left: 5px;
        }
        .stamp-top {
            position: absolute;
            top: 11px;
            width: 83px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #003893;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .stamp-mid {
            position: absolute;
            top: 30px;
            width: 83px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #003893;
            letter-spacing: 1px;
        }
        .stamp-sub {
            position: absolute;
            top: 52px;
            width: 83px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #003893;
        }
        .stamp-arabic {
            position: absolute;
            bottom: 11px;
            width: 83px;
            text-align: center;
            font-size: 6.5px;
            font-weight: bold;
            color: #003893;
        }
        .sig-person {
            font-size: 10px;
            font-weight: bold;
            color: #003893;
            margin-top: 3px;
        }

        /* Page footer bar */
        .page-footer {
            border-top: 1px solid rgba(201,162,39,0.4);
            padding-top: 4px;
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            line-height: 1.4;
        }
        .page-footer strong {
            color: #003893;
        }
    </style>
</head>
<body>
@php
    $student = $convocation->student;
    $currentExam = $convocation->exam;
    $session = $currentExam->examSession;

    $allConvocations = \App\Models\Convocation::where('student_id', $student->id)
        ->whereHas('exam', function ($query) use ($session) {
            $query->where('exam_session_id', $session->id);
        })
        ->with(['exam.module', 'exam.room'])
        ->get()
        ->sortBy(function ($c) {
            return $c->exam->date . ' ' . $c->exam->start_time;
        });

    $academicYearName = $session->academicYear->name ?? '2025-2026';
    $sessionName = $session->name;

    $levelText = match(intval($student->group->level ?? 0)) {
        1 => 'Première année',
        2 => 'Deuxième année',
        3 => 'Troisième année',
        4 => 'Quatrième année',
        5 => 'Cinquième année',
        default => ($student->group->level ? $student->group->level . 'ème année' : 'Troisième année')
    };

    // Bulletproof Base64 Logo Loading
    $logoPath = public_path('images/logo_upf.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = base64_encode(file_get_contents($logoPath));
    }

    // Dynamic URL and offline-friendly QR code encoding
    $verifyUrl = route('admin.convocations.verify', $convocation->reference);
    $qrCodeBase64 = '';
    try {
        $qrCodeBase64 = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate($verifyUrl));
    } catch (\Exception $e) {
        $qrCodeBase64 = '';
    }
@endphp

<div class="page-wrapper">
    <div class="doc-border">
        <div class="watermark">UPF CONVOCATION</div>

        <!-- ===== HEADER ===== -->
        <table class="header-table">
            <tr>
                <td class="hdr-left">
                    ROYAUME DU MAROC<br>
                    UNIVERSITÉ PRIVÉE DE FÈS<br>
                    École Supérieure d'Ingénierie<br>
                    et de Technologie de Fès
                </td>
                <td class="hdr-center">
                    @if($logoBase64)
                        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo UPF">
                    @else
                        <img src="{{ public_path('images/logo_upf.png') }}" alt="Logo UPF">
                    @endif
                    <div class="hdr-center-title">UNIVERSITÉ PRIVÉE DE FÈS</div>
                </td>
                <td class="hdr-right">
                    المملكة المغربية<br>
                    الجامعة الخاصة لفاس<br>
                    المدرسة العليا للهندسة<br>
                    والتكنولوجيا بفاس
                </td>
            </tr>
        </table>

        <div class="sub-divider"></div>

        <!-- ===== CONVOCATION TITLE ===== -->
        <div class="conv-title">
            <h1 class="conv-title-main">Convocation aux Examens</h1>
            <div class="conv-title-session">Année Académique : {{ $academicYearName }} — Session : {{ $sessionName }}</div>
        </div>

        <!-- ===== STUDENT INFO ===== -->
        <table class="student-table">
            <tr>
                <td class="s-label">Code d'Inscription (CNE)</td>
                <td class="s-value blue">: {{ $student->student_number ?? 'N/A' }}</td>
                <td class="s-label">N° de C.I.N</td>
                <td class="s-value">: {{ $student->cin ?? 'Non renseigné' }}</td>
            </tr>
            <tr>
                <td class="s-label">Nom &amp; Prénom</td>
                <td class="s-value blue" style="text-transform: uppercase;">: {{ $student->user->name ?? 'N/A' }}</td>
                <td class="s-label">Niveau d'Études</td>
                <td class="s-value">: {{ $levelText }}</td>
            </tr>
            <tr>
                <td class="s-label">Filière d'Études</td>
                <td class="s-value blue" colspan="3">: {{ $student->group->filiere->name ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- ===== NOTICE ===== -->
        <div class="notice">
            Vous êtes prié(e) de vous présenter aux dates et heures fixées ci-dessous pour passer vos épreuves :
        </div>

        <!-- ===== EXAMS TABLE ===== -->
        <table class="exams-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 13%;">Horaire</th>
                    <th style="width: 32%;" class="text-left">Module / Matière</th>
                    <th style="width: 21%;" class="text-left">Enseignant</th>
                    <th style="width: 13%;">Salle</th>
                    <th style="width: 9%;">Place</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allConvocations as $c)
                    @php
                        $sched = \App\Models\Schedule::where('group_id', $c->exam->group_id)
                            ->where('module_id', $c->exam->module_id)
                            ->with('professor.user')
                            ->first();
                        $profName = $sched && $sched->professor && $sched->professor->user
                            ? $sched->professor->user->name
                            : 'Non spécifié';
                        $seatNumberStr = $c->seat_number
                            ? str_replace('Place ', '', $c->seat_number)
                            : ((($student->id * 17) + ($c->exam->id * 11)) % 55 + 1);
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($c->exam->date)->format('d/m/Y') }}</td>
                        <td class="bold">{{ date('H:i', strtotime($c->exam->start_time)) }} - {{ $c->exam->end_time }}</td>
                        <td class="text-left bold" style="color: #003893;">{{ $c->exam->module->name }}</td>
                        <td class="text-left" style="text-transform: uppercase; font-size: 8.5px;">{{ $profName }}</td>
                        <td class="bold" style="color: #9b1d6e;">{{ $c->exam->room->name ?? 'TBD' }}</td>
                        <td class="bold" style="font-size: 11px; background: rgba(201,162,39,0.06);">{{ $seatNumberStr }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ===== RÈGLEMENT ===== -->
        <div class="rules-section">
            <div class="rules-title">Règlement des Examens — Extraits</div>
            <div class="rules-text">
                <ul>
                    <li>L'usage des téléphones portables et de tout appareil connecté est strictement interdit et assimilé à une tentative de fraude.</li>
                    <li>Chaque étudiant doit se munir de sa propre carte d'étudiant ou de sa CIN. Aucun prêt de matériel n'est autorisé entre candidats.</li>
                    <li>Tout retard supérieur à 20 minutes après l'ouverture des enveloppes exclut le candidat de l'épreuve.</li>
                    <li>La sortie définitive de la salle n'est permise qu'après l'écoulement des 30 premières minutes de l'épreuve.</li>
                    <li>Toute absence non justifiée médicalement dans les 48 heures entraîne l'attribution de la note zéro.</li>
                    <li>Toute tentative de fraude fera l'objet d'un rapport immédiat et d'une comparution devant le Conseil de Discipline.</li>
                </ul>
            </div>
        </div>

        <!-- ===== FOOTER / SIGNATURE ===== -->
        <div class="footer-section">
            <table class="footer-sig-table">
                <tr>
                    <!-- QR Code left -->
                    <td class="footer-left-cell">
                        @if($qrCodeBase64)
                            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" width="70" height="70">
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($verifyUrl) }}" alt="QR Code" width="70" height="70">
                        @endif
                        <div class="footer-left-note" style="margin-top: 4px;">
                            <p>Vérification Officielle UPF</p>
                            <p style="font-size: 6px; color: #94a3b8; font-family: monospace;">Réf : {{ $convocation->reference }}</p>
                        </div>
                    </td>

                    <!-- Signature + Stamp right -->
                    <td class="footer-right-cell">
                        <div style="display: inline-block; text-align: center;">
                            <div class="sig-name-title">Le Directeur des Études &amp; des Affaires Académiques</div>

                            <!-- Stamp -->
                            <div class="stamp-circle">
                                <div class="stamp-inner">
                                    <div class="stamp-top">UNIVERSITE PRIVEE DE FES</div>
                                    <div class="stamp-mid">★ UPF ★</div>
                                    <div class="stamp-sub">SCOLARITÉ</div>
                                    <div class="stamp-arabic">الجامعة الخاصة لفاس</div>
                                </div>
                            </div>

                            <div class="sig-person">FADOUA KHALOUQ</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Page footer bar -->
            <div class="page-footer">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc &nbsp;|&nbsp;
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web : upf.ac.ma &nbsp;|&nbsp; Email : contact@upf.ac.ma
            </div>
        </div>

    </div><!-- /doc-border -->
</div><!-- /page-wrapper -->

</body>
</html>
