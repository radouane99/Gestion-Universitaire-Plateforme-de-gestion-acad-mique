<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Relevé de Notes - {{ $student->student_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15px 20px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { border: none; vertical-align: middle; padding: 0; }
        .hdr-left { width: 30%; font-size: 7px; font-weight: bold; color: #003399; line-height: 1.5; }
        .hdr-center { width: 40%; text-align: center; }
        .hdr-center img { height: 46px; display: block; margin: 0 auto 2px auto; }
        .hdr-center-name { font-size: 8px; font-weight: bold; color: #003399; }
        .hdr-right { width: 30%; text-align: right; font-size: 7px; font-weight: bold; color: #db2777; line-height: 1.5; direction: rtl; }
        .logo-text {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .sub-logo {
            font-size: 11px;
            font-weight: bold;
            color: #db2777;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 10px;
            page-break-inside: avoid;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 4px;
            border: none;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .grades-table tr {
            page-break-inside: avoid;
        }
        .grades-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #94a3b8;
            font-size: 11px;
        }
        .grades-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }
        .semester-row {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
        }
        .summary-box {
            width: 100%;
            margin-top: 30px;
            border: 2px solid #1e3a8a;
            padding: 15px;
            text-align: center;
            background-color: #f8fafc;
            page-break-inside: avoid;
        }
        .summary-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: none; vertical-align: bottom; padding: 0; }
        .qr-code {
            position: absolute;
            top: 20px;
            right: 40px;
        }
        .stamp-circle {
            width: 80px; height: 80px;
            border: 2px double #1e3a8a;
            border-radius: 50%;
            display: inline-block;
            position: relative;
        }
        .stamp-inner {
            width: 70px; height: 70px;
            border: 1px solid #1e3a8a;
            border-radius: 50%;
            position: absolute;
            top: 4px; left: 4px;
        }
        .stamp-top { position: absolute; top: 9px; width: 70px; text-align: center; font-size: 5px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .stamp-mid { position: absolute; top: 25px; width: 70px; text-align: center; font-size: 11px; font-weight: bold; color: #1e3a8a; }
        .stamp-sub { position: absolute; top: 44px; width: 70px; text-align: center; font-size: 5px; font-weight: bold; color: #1e3a8a; }
        .stamp-arabic { position: absolute; bottom: 9px; width: 70px; text-align: center; font-size: 5.5px; font-weight: bold; color: #1e3a8a; }
        .page-footer-bar { border-top: 1px solid #e2e8f0; padding-top: 4px; text-align: center; font-size: 7px; color: #94a3b8; margin-top: 6px; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="hdr-left">
                    ROYAUME DU MAROC<br>
                    UNIVERSITÉ PRIVÉE DE FÈS<br>
                    École Supérieure d'Ingénierie<br>
                    et de Technologie de Fès
                </td>
                <td class="hdr-center">
                    <img src="{{ public_path('images/logo_upf.png') }}" alt="Logo UPF">
                    <div class="hdr-center-name">UNIVERSITÉ PRIVÉE DE FÈS</div>
                </td>
                <td class="hdr-right">
                    المملكة المغربية<br>
                    الجامعة الخاصة لفاس<br>
                    المدرسة العليا للهندسة<br>
                    والتكنولوجيا بفاس
                </td>
            </tr>
        </table>
    </div>

    <div class="title">Relevé de Notes Annuel</div>

    <div class="student-info">
        <table>
            <tr>
                <td width="20%" class="font-bold">Nom & Prénom :</td>
                <td width="30%" style="text-transform: uppercase;">{{ $student->user->name }}</td>
                <td width="20%" class="font-bold">Année Académique :</td>
                <td width="30%">{{ $academicYear->name }}</td>
            </tr>
            <tr>
                <td class="font-bold">Code Apogée (CNE) :</td>
                <td>{{ $student->student_number }}</td>
                <td class="font-bold">Filière :</td>
                <td>{{ $student->group->filiere->name }}</td>
            </tr>
            <tr>
                <td class="font-bold">CIN :</td>
                <td>{{ $student->cin ?? 'Non renseigné' }}</td>
                <td class="font-bold">Niveau :</td>
                <td>{{ $student->group->level }}ème Année</td>
            </tr>
        </table>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th width="15%">Code</th>
                <th width="45%">Intitulé du Module</th>
                <th width="10%" class="text-center">Note / 20</th>
                <th width="15%" class="text-center">Résultat</th>
                <th width="15%" class="text-center">Date Ex. / Val.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semesters as $sem)
                @php
                    $semModules = $modules->where('semester_id', $sem->id);
                    $semData = $studentData['semesters'][$sem->id] ?? null;
                @endphp

                <!-- Semester Header Row -->
                <tr class="semester-row">
                    <td colspan="5">Semestre {{ $sem->name }}</td>
                </tr>

                <!-- Modules -->
                @foreach($semModules as $mod)
                    @php
                        $modData = $studentData['modules'][$mod->id] ?? null;
                        $grade = $modData && $modData['final_grade'] !== null ? number_format($modData['final_grade'], 2, ',', ' ') : 'N/A';
                        $dec = $modData ? $modData['decision'] : '-';
                        if ($semData && $semData['decision'] === 'V' && $modData && $modData['decision'] === 'NV') {
                            $dec = 'VC'; // Validé par compensation
                        }
                    @endphp
                    <tr>
                        <td>{{ $mod->code }}</td>
                        <td>{{ $mod->name }}</td>
                        <td class="text-center font-bold">{{ $grade }}</td>
                        <td class="text-center">{{ $dec }}</td>
                        <td class="text-center">{{ $modData['val_date'] ?? '-' }}</td>
                    </tr>
                @endforeach

                <!-- Semester Summary Row -->
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="2" class="text-right">Bilan Semestre {{ $sem->name }} :</td>
                    <td class="text-center">
                        {{ $semData && $semData['average'] !== null ? number_format($semData['average'], 2, ',', ' ') : 'N/A' }}
                    </td>
                    <td class="text-center">{{ $semData['decision'] ?? '-' }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <p><span class="font-bold">Moyenne Annuelle :</span> {{ $studentData['annual_average'] !== null ? number_format($studentData['annual_average'], 2, ',', ' ') . ' / 20' : 'N/A' }}</p>
        <p><span class="font-bold">Mention :</span> {{ $mention }}</p>
        <p><span class="font-bold">Décision du Jury :</span> <span style="text-transform: uppercase;">{{ $studentData['annual_decision'] ?? 'Non Statué' }}</span></p>
    </div>

    <div class="footer">
        <table>
            <tr>
                <!-- QR Left -->
                <td style="width:40%; vertical-align:bottom;">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code"
                         style="border:1px solid #cbd5e1; padding:3px; background:white; border-radius:4px; display:block; width:75px; height:75px;">
                    <div style="font-size:7px; color:#64748b; font-weight:bold; margin-top:3px;">Scannez pour vérifier l'authenticité</div>
                </td>
                <!-- Signature Right -->
                <td style="width:60%; text-align:right; vertical-align:top;">
                    <div style="font-size:10px; font-style:italic; color:#475569; margin-bottom:3px;">Fès, le {{ now()->format('d/m/Y') }}</div>
                    <div style="font-size:10.5px; font-weight:bold; color:#1e3a8a; text-decoration:underline; margin-bottom:5px;">Le Doyen de la Faculté</div>
                    <div class="stamp-circle">
                        <div class="stamp-inner">
                            <div class="stamp-top">UNIVERSITE PRIVEE DE FES</div>
                            <div class="stamp-mid">★ UPF ★</div>
                            <div class="stamp-sub">SCOLARITÉ</div>
                            <div class="stamp-arabic">الجامعة الخاصة لفاس</div>
                        </div>
                    </div>
                    <div style="font-size:9px; font-weight:bold; color:#1e3a8a; margin-top:3px;">Le Doyen</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footer-bar">
        <strong style="color:#1e3a8a;">Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc &nbsp;|&nbsp;
        Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web : upf.ac.ma &nbsp;|&nbsp; Email : contact@upf.ac.ma
    </div>

</body>
</html>
