<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $transcriptTitle }} - UPF</title>
    <style>
        @page {
            margin: 15mm 18mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #001A4D;
            margin: 0;
            padding: 0;
            background: white;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #003399;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-logo-side {
            width: 50%;
            text-align: left;
        }
        .logo-text {
            font-size: 26px;
            font-weight: 900;
            color: #003399;
            letter-spacing: -1px;
            line-height: 1;
        }
        .logo-sub {
            font-size: 8px;
            font-weight: bold;
            color: #B00D5D;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }
        .header-info-side {
            width: 50%;
            text-align: right;
            font-size: 10px;
            color: #475569;
        }
        
        .title {
            text-align: center;
            margin: 12px 0;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            color: #003399;
            letter-spacing: 0.5px;
        }
        
        .student-info-table {
            width: 100%;
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .student-info-label {
            font-weight: bold;
            color: #475569;
            width: 18%;
            font-size: 10.5px;
        }
        .student-info-value {
            color: #0f172a;
            font-weight: bold;
            font-size: 10.5px;
        }

        .semester-block {
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .semester-title-table {
            width: 100%;
            background: #003399;
            color: white;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grades-table th {
            background: #f1f5f9;
            color: #475569;
            padding: 5px 12px;
            text-align: left;
            font-size: 9.5px;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            font-weight: 800;
        }
        .grades-table td {
            padding: 5px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
            color: #1e293b;
        }
        .grades-table tr:last-child td {
            border-bottom: none;
        }
        
        .final-result-box {
            margin-top: 12px;
            border: 2px solid #003399;
            background: #f8fafc;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
            page-break-inside: avoid;
        }
        .final-result-title {
            font-size: 13px;
            font-weight: 800;
            color: #003399;
        }
        .final-result-gpa {
            font-size: 14px;
            font-weight: 900;
        }
        .final-result-decision {
            font-size: 11px;
            margin-top: 3px;
            color: #475569;
            font-weight: 750;
        }
        
        .validation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .qr-col {
            width: 60%;
            text-align: left;
            vertical-align: middle;
        }
        .qr-code-img {
            width: 65px;
            height: 65px;
            display: inline-block;
            vertical-align: middle;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: white;
        }
        .qr-text {
            display: inline-block;
            vertical-align: middle;
            margin-left: 10px;
            font-size: 8.5px;
            color: #64748b;
            max-width: 250px;
            line-height: 1.3;
        }
        .signature-col {
            width: 40%;
            text-align: right;
            vertical-align: top;
            font-size: 10px;
            color: #1e293b;
        }
        
        .footer-note {
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            margin-top: 12px;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="header-logo-side" style="vertical-align: middle;">
                    @if($setting && $setting->logo_path && file_exists(public_path('storage/' . $setting->logo_path)))
                        <img src="{{ public_path('storage/' . $setting->logo_path) }}" style="height: 48px; max-width: 220px; display: block;">
                    @else
                        <div class="logo-text">UPF</div>
                        <div class="logo-sub">Université Privée de Fès</div>
                    @endif
                </td>
                <td class="header-info-side">
                    <b>{{ __('Année Académique') }}:</b> {{ $student && $student->academicYear ? $student->academicYear->name : ($setting->academic_year ?? '2025/2026') }}<br>
                    <b>{{ __('Session') }}:</b> {{ __('Principale') }}<br>
                    <b>{{ __('Date d\'édition') }}:</b> {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        <!-- Document Title -->
        <h1 class="title">{{ $transcriptTitle }}</h1>

        <!-- Student Info Card -->
        <table class="student-info-table">
            <tr>
                <td class="student-info-label">{{ __('Nom & Prénom') }} :</td>
                <td class="student-info-value">{{ $request->user->name }}</td>
                <td class="student-info-label" style="width: 15%;">{{ __('N° Étudiant') }} :</td>
                <td class="student-info-value" style="width: 20%;">STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</td>
            </tr>
            <tr>
                <td class="student-info-label">{{ __('Filière') }} :</td>
                <td class="student-info-value" colspan="3">
                    {{ $student && $student->group && $student->group->filiere ? $student->group->filiere->name : 'Génie Informatique / SIGL' }}
                </td>
            </tr>
        </table>

        <!-- Semesters & Grades List -->
        @forelse($gradesBySemester as $semesterName => $grades)
        <div class="semester-block">
            @php
                $semGradesCount = $grades->whereNotNull('final_grade')->count();
                $semGPA = $semGradesCount > 0 ? $grades->whereNotNull('final_grade')->avg('final_grade') : 0;
            @endphp
            <table class="semester-title-table">
                <tr>
                    <td style="text-align: left; border: none; padding: 0;">📚 {{ $semesterName }}</td>
                    <td style="text-align: right; border: none; padding: 0;">Moyenne: {{ number_format($semGPA, 2) }} / 20</td>
                </tr>
            </table>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">{{ __('Code Module') }}</th>
                        <th style="width: 50%;">{{ __('Intitulé du Module') }}</th>
                        <th style="width: 15%; text-align: center;">{{ __('Note / 20') }}</th>
                        <th style="width: 15%; text-align: center;">{{ __('Résultat') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $grade)
                    <tr>
                        <td><b>{{ $grade->module->code ?? 'MOD-' . $grade->module->id }}</b></td>
                        <td>{{ $grade->module->name }}</td>
                        <td style="text-align: center; font-weight: bold; color: {{ $grade->final_grade >= 10 ? '#059669' : ($grade->final_grade !== null ? '#e11d48' : '#64748b') }};">
                            {{ $grade->final_grade ? number_format($grade->final_grade, 2) : '--' }}
                        </td>
                        <td style="text-align: center;">
                            @if($grade->final_grade >= 10)
                                <span style="color: #059669; font-weight: bold;">Validé</span>
                            @elseif($grade->final_grade !== null)
                                <span style="color: #e11d48; font-weight: bold;">Rattrapage</span>
                            @else
                                <span style="color: #64748b; font-style: italic;">En Cours</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @empty
        <div style="margin: 30px 0; text-align: center; color: #94a3b8; font-style: italic; font-size: 12px; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 8px;">
            Aucune note active enregistrée pour cette année universitaire.
        </div>
        @endforelse

        <!-- Final Result Block -->
        <div class="final-result-box">
            <span class="final-result-title">
                {{ $isAnnual ? __('RÉSULTAT DE L\'ANNÉE') : __('RÉSULTAT DU SEMESTRE') }} :
            </span>
            <span class="final-result-gpa" style="color: {{ $yearlyGPA >= 10 ? '#059669' : '#e11d48' }};">
                MOYENNE GÉNÉRALE = {{ number_format($yearlyGPA, 2) }} / 20
            </span>
            <div class="final-result-decision">
                Décision du Jury : 
                <span style="color: {{ $yearlyGPA >= 10 ? '#059669' : ($yearlyGPA > 0 ? '#e11d48' : '#475569') }};">
                    {{ $yearlyGPA >= 10 ? __('ADMIS(E) AU NIVEAU SUPÉRIEUR') : ($yearlyGPA > 0 ? __('AJOURNÉ(E)') : __('EN COURS D\'ÉVALUATION')) }}
                </span>
            </div>
        </div>

        <!-- Verification QR & Signature Column -->
        <table class="validation-table">
            <tr>
                <td class="qr-col">
                    <img src="data:image/svg+xml;base64,{!! $qrCode !!}" class="qr-code-img" alt="Verification QR">
                    <div class="qr-text">
                        <b>Document numérique officiel</b><br>
                        Généré par le portail universitaire de l'UPF.<br>
                        Scannez le code QR ci-dessus pour vérifier l'authenticité de ce relevé de notes en ligne.
                    </div>
                </td>
                <td class="signature-col" style="vertical-align: top;">
                    Fait à Fès, le {{ $request->updated_at ? $request->updated_at->format('d/m/Y') : now()->format('d/m/Y') }}<br>
                    <b style="display: block; margin-top: 5px; text-transform: uppercase;">Le Doyen de l'Université</b>
                    @if($setting && $setting->signature_path && file_exists(public_path('storage/' . $setting->signature_path)))
                        <img src="{{ public_path('storage/' . $setting->signature_path) }}" style="height: 42px; max-width: 160px; display: block; margin-left: auto; margin-top: 5px;">
                    @else
                        <div style="margin-top: 12px; font-size: 8px; color: #94a3b8; font-style: italic; border: 1px dashed #e2e8f0; padding: 4px; display: inline-block; border-radius: 4px; text-align: center;">
                            Signé numériquement<br>
                            par le Doyen UPF
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Footer legal note -->
        <div class="footer-note">
            Université Privée de Fès - Route d'Imouzzer, Fès, Maroc - Tél: +212 535 600 800 - Email: contact@upf.ac.ma - www.upf.ac.ma
        </div>
    </div>
</body>
</html>
