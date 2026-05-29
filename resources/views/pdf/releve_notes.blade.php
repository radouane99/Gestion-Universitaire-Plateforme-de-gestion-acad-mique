<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Relevé de Notes - {{ $student->student_number }}</title>
    <style>
        @page {
            margin: 20px 40px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            vertical-align: middle;
        }
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
        }
        .summary-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .footer table {
            width: 100%;
        }
        .footer td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .qr-code {
            position: absolute;
            top: 20px;
            right: 40px;
        }
    </style>
</head>
<body>

    <div class="qr-code">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
    </div>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="logo-text">UNIVERSITÉ DE PORTFOLIO (UPF)</div>
                    <div class="sub-logo">FACULTÉ DES SCIENCES & DE L'ÉDUCATION</div>
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
                <td>
                    <p class="font-bold">Fait à Rabat, le {{ now()->format('d/m/Y') }}</p>
                    <p>Le Chef de Département</p>
                </td>
                <td>
                    <p class="font-bold">&nbsp;</p>
                    <p>Le Doyen de la Faculté</p>
                </td>
            </tr>
        </table>
    </div>
    
    <div style="margin-top: 30px; font-size: 9px; color: #64748b; text-align: center;">
        Ce document est délivré par le système de gestion académique UPF.<br>
        Vous pouvez vérifier son authenticité en scannant le code QR en haut à droite.
    </div>

</body>
</html>
