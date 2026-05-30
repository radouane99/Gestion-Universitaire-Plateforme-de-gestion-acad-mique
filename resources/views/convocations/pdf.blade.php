<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convocation — {{ $convocation->reference }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #000;
            background: #fff;
            font-size: 11.5px;
            line-height: 1.4;
        }

        /* ======================== PAGE WRAPPER ======================== */
        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            overflow: hidden;
            padding: 7mm 10mm 7mm 10mm;
            position: relative;
        }

        /* ======================== HEADER ======================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .hdr-logo-cell {
            width: 60px;
            vertical-align: middle;
        }
        .hdr-logo-cell img {
            height: 48px;
            display: block;
        }
        .hdr-text-cell {
            padding-left: 10px;
            vertical-align: middle;
        }
        .hdr-univ-name {
            font-size: 19px;
            font-weight: bold;
            letter-spacing: 0.3px;
            color: #000;
            line-height: 1.1;
        }
        .hdr-univ-arabic {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
            color: #000;
        }
        .hdr-right-cell {
            width: 28%;
            text-align: right;
            vertical-align: top;
        }
        .hdr-ref {
            font-size: 7.5px;
            color: #444;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* ======================== TITLE ======================== */
        .conv-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .conv-title-main {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .conv-title-session {
            font-size: 16px;
            font-weight: bold;
            margin-top: 3px;
        }

        /* ======================== STUDENT INFO ======================== */
        .student-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 12.5px;
            border-collapse: collapse;
        }
        .student-table td {
            padding: 3px 0;
        }
        .s-label {
            font-weight: bold;
            width: 22%;
        }
        .s-value {
            width: 78%;
        }

        /* ======================== NOTICE TEXT ======================== */
        .notice {
            font-size: 12.5px;
            margin-bottom: 10px;
        }

        /* ======================== EXAMS TABLE ======================== */
        .exams-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10.5px;
        }
        .exams-table thead tr {
            background-color: #f0f0f0;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }
        .exams-table th {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
        }
        .exams-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
        }
        .exams-table td.text-left {
            text-align: left;
            padding-left: 7px;
        }

        /* ======================== RULES ======================== */
        .rules-section {
            margin-bottom: 10px;
        }
        .rules-title {
            font-size: 12.5px;
            font-weight: bold;
            border-bottom: 1.5px solid #000;
            padding-bottom: 3px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .rules-text {
            font-size: 7.8px;
            line-height: 1.4;
            color: #111;
            text-align: justify;
        }
        .rules-text li {
            margin-bottom: 1px;
            list-style: none;
            padding-left: 0;
        }
        .rules-text li::before {
            content: "- ";
        }

        /* ======================== FOOTER / SIGNATURE ======================== */
        .footer-sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .footer-sig-table td {
            vertical-align: bottom;
            padding: 0;
        }
        .footer-left-cell {
            width: 45%;
            vertical-align: bottom;
        }
        .footer-left-note {
            font-size: 8px;
            color: #555;
        }
        .footer-right-cell {
            width: 55%;
            text-align: right;
            vertical-align: top;
        }
        .sig-name-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        /* Stamp circle */
        .stamp-circle {
            width: 100px;
            height: 100px;
            border: 2px double #1d4ed8;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background: rgba(29,78,216,0.02);
        }
        .stamp-inner {
            width: 88px;
            height: 88px;
            border: 1px solid #1d4ed8;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            left: 5px;
        }
        .stamp-top {
            position: absolute;
            top: 12px;
            width: 88px;
            text-align: center;
            font-size: 6px;
            font-weight: bold;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .stamp-mid {
            position: absolute;
            top: 32px;
            width: 88px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 1px;
        }
        .stamp-sub {
            position: absolute;
            top: 54px;
            width: 88px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #1d4ed8;
        }
        .stamp-arabic {
            position: absolute;
            bottom: 12px;
            width: 88px;
            text-align: center;
            font-size: 7px;
            font-weight: bold;
            color: #1d4ed8;
        }
        .sig-person {
            font-size: 11px;
            font-weight: bold;
            color: #1d4ed8;
            margin-top: 4px;
        }

        /* Page footer bar */
        .page-footer {
            border-top: 1px solid #ccc;
            padding-top: 3px;
            text-align: center;
            font-size: 7px;
            color: #777;
            margin-top: 6px;
        }
        .page-footer strong {
            color: #000;
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
@endphp

<div class="page-wrapper">

    <!-- ===== HEADER ===== -->
    <table class="header-table">
        <tr>
            <td class="hdr-logo-cell">
                <img src="{{ public_path('images/logo_upf.png') }}" alt="UPF Logo">
            </td>
            <td class="hdr-text-cell">
                <div class="hdr-univ-name">UNIVERSITÉ PRIVÉE DE FÈS</div>
                <div class="hdr-univ-arabic">الجامعة الخاصة لفاس</div>
            </td>
            <td class="hdr-right-cell">
                <div style="margin-bottom: 3px;">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->generate(route('admin.convocations.verify', $convocation->reference)) !!}
                </div>
                <div class="hdr-ref">Réf : {{ $convocation->reference }}</div>
            </td>
        </tr>
    </table>

    <!-- ===== CONVOCATION TITLE ===== -->
    <div class="conv-title">
        <div class="conv-title-main">Convocation aux Examens</div>
        <div class="conv-title-session">{{ $academicYearName }} — Session {{ $sessionName }}</div>
    </div>

    <!-- ===== STUDENT INFO ===== -->
    <table class="student-table">
        <tr>
            <td class="s-label">Matricule</td>
            <td class="s-value">: {{ $student->student_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="s-label">Nom &amp; Prénom</td>
            <td class="s-value" style="text-transform: uppercase;">: {{ $student->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="s-label">Filière</td>
            <td class="s-value">: {{ $student->group->filiere->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="s-label">Niveau</td>
            <td class="s-value">: {{ $levelText }}</td>
        </tr>
    </table>

    <!-- ===== NOTICE ===== -->
    <div class="notice">
        Vous êtes prié(e) de vous présenter aux dates et heures suivantes pour les épreuves ci-dessous :
    </div>

    <!-- ===== EXAMS TABLE ===== -->
    <table class="exams-table">
        <thead>
            <tr>
                <th style="width:11%;">Date</th>
                <th style="width:13%;">Horaire</th>
                <th style="width:35%;" class="text-left" style="text-align:left; padding-left:7px;">Matière</th>
                <th style="width:20%;" class="text-left" style="text-align:left; padding-left:7px;">Enseignant</th>
                <th style="width:12%;">Salle</th>
                <th style="width:9%;">Place</th>
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
                    <td>{{ date('H:i', strtotime($c->exam->start_time)) }} - {{ $c->exam->end_time }}</td>
                    <td class="text-left" style="font-weight:bold;">{{ $c->exam->module->name }}</td>
                    <td class="text-left" style="text-transform:uppercase; font-size:9.5px;">{{ $profName }}</td>
                    <td style="font-weight:bold;">{{ $c->exam->room->name ?? 'TBD' }}</td>
                    <td>{{ $seatNumberStr }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ===== RÈGLEMENT ===== -->
    <div class="rules-section">
        <div class="rules-title">Règlement des Examens</div>
        <div class="rules-text">
            <ul>
                <li>L'usage des téléphones portables, tablettes ou autres appareils électroniques est strictement interdit en salle d'examen.</li>
                <li>Chaque étudiant doit se munir de tous les articles de bureau nécessaires (stylos, crayons, gomme, règle, etc.). L'échange entre étudiants est interdit.</li>
                <li>Tout étudiant en retard de plus de 20 minutes après la distribution des sujets ne peut être admis en salle d'examen.</li>
                <li>Aucun étudiant ne peut quitter définitivement la salle que 30 minutes au moins après la distribution des sujets, sur autorisation de l'enseignant.</li>
                <li>Il est strictement interdit de quitter temporairement la salle pendant le déroulement de l'épreuve.</li>
                <li>Toute fraude dûment constatée donne lieu à un zéro et à un rapport disciplinaire transmis à la Direction dans un délai de 48 heures.</li>
                <li>Toute copie non rendue à l'heure fixée par les surveillants est affectée d'un zéro.</li>
            </ul>
        </div>
    </div>

    <!-- ===== SIGNATURE & CACHET ===== -->
    <table class="footer-sig-table">
        <tr>
            <td class="footer-left-cell">
                <div class="footer-left-note">
                    Université Privée de Fès — Service Scolarité<br>
                    Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc
                </div>
            </td>
            <td class="footer-right-cell">
                <div style="display:inline-block; text-align:center;">
                    <div class="sig-name-title">Chargée de la Scolarité et des Affaires Estudiantines</div>

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

    <!-- ===== PAGE FOOTER ===== -->
    <div class="page-footer">
        <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc &nbsp;|&nbsp;
        Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web : upf.ac.ma &nbsp;|&nbsp; Email : contact@upf.ac.ma
    </div>

</div>
</body>
</html>
