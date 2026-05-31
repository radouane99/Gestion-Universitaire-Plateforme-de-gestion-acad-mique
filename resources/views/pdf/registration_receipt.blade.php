<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu d'Inscription — {{ $student->student_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            background: #ffffff;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.4;
        }

        /* ======================== OUTER WRAPPER ======================== */
        .page-wrapper {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: white;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* ======================== BORDER ======================== */
        .doc-border {
            border: 7px double #003893;
            padding: 15px 20px;
            position: absolute;
            top: 10mm;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60px;
            color: rgba(0, 56, 147, 0.035);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
            letter-spacing: 2px;
            font-family: 'DejaVu Sans', sans-serif;
        }

        /* ======================== HEADER ======================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #003893;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .hdr-left {
            width: 35%;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            color: #003893;
            line-height: 1.5;
        }
        .hdr-center {
            width: 25%;
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
            width: 40%;
            text-align: right;
            font-size: 7.5px;
            font-weight: bold;
            color: #9b1d6e;
            line-height: 1.5;
            white-space: nowrap;
        }

        /* Sub-line under institution names */
        .sub-divider {
            height: 2px;
            background: linear-gradient(to right, #003893, #c9a227, #9b1d6e);
            border-radius: 2px;
            margin-bottom: 8px;
        }

        /* ======================== TITLE ======================== */
        .doc-title {
            text-align: center;
            margin: 6px 0 8px 0;
        }
        .doc-title h1 {
            font-size: 18px;
            font-weight: bold;
            color: #003893;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #9b1d6e;
            display: inline-block;
            padding-bottom: 3px;
        }

        /* ======================== BODY TEXT ======================== */
        .cert-body {
            font-size: 11.5px;
            line-height: 1.55;
            text-align: justify;
            margin-bottom: 8px;
        }
        .cert-body p {
            margin-bottom: 4px;
        }

        /* ======================== INFO TABLE ======================== */
        .info-table {
            width: 100%;
            margin: 6px 0;
            border-collapse: collapse;
            border: 1px solid rgba(0, 56, 147, 0.35);
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table td {
            padding: 5px 10px;
            font-size: 11px;
            border-bottom: 1px solid rgba(0, 56, 147, 0.15);
        }
        .info-label {
            font-weight: bold;
            width: 38%;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .info-value {
            font-weight: bold;
            color: #0f172a;
        }
        .info-value.blue {
            color: #003893;
        }
        .info-value.pink {
            color: #9b1d6e;
        }

        /* ======================== FOOTER AREA ======================== */
        .footer-section {
            position: absolute;
            bottom: 12px;
            left: 20px;
            right: 20px;
        }

        /* Signature table */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .sig-table td {
            vertical-align: bottom;
            padding: 0;
        }
        .qr-cell {
            width: 42%;
            text-align: left;
            vertical-align: bottom;
        }
        .qr-cell img {
            border: 1px solid #003893;
            padding: 3px;
            background: white;
            border-radius: 4px;
            display: block;
        }
        .qr-cell p {
            font-size: 7px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
        }
        .sig-cell {
            width: 58%;
            text-align: right;
            vertical-align: top;
        }
        .sig-date {
            font-size: 10px;
            font-style: italic;
            color: #475569;
            margin-bottom: 3px;
        }
        .sig-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #003893;
            text-decoration: underline;
            margin-bottom: 4px;
        }

        /* Stamp circle */
        .stamp-wrapper {
            display: inline-block;
            text-align: center;
        }
        .stamp-circle {
            width: 90px;
            height: 90px;
            border: 2px double #003893;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background: rgba(0,56,147,0.03);
        }
        .stamp-inner {
            width: 80px;
            height: 80px;
            border: 1px solid #003893;
            border-radius: 50%;
            position: absolute;
            top: 4px;
            left: 4px;
        }
        .stamp-top {
            position: absolute;
            top: 10px;
            width: 80px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #003893;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .stamp-logo {
            position: absolute;
            top: 28px;
            width: 80px;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            color: #003893;
            letter-spacing: 1px;
        }
        .stamp-name {
            position: absolute;
            top: 50px;
            width: 80px;
            text-align: center;
            font-size: 5px;
            font-weight: bold;
            color: #003893;
        }
        .stamp-arabic {
            position: absolute;
            bottom: 10px;
            width: 80px;
            text-align: center;
            font-size: 6px;
            font-weight: bold;
            color: #003893;
            white-space: nowrap;
        }

        /* Page footer bar */
        .footer-bar {
            border-top: 1px solid rgba(0, 56, 147, 0.4);
            padding-top: 4px;
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            line-height: 1.4;
        }
        .footer-bar strong {
            color: #003893;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/logo_upf.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = base64_encode(file_get_contents($logoPath));
    }
@endphp

<div class="page-wrapper">
    <div class="doc-border">
        <div class="watermark">INSCRIPTION CONFIRMÉE</div>

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
                    @arabic('المملكة المغربية')<br>
                    @arabic('الجامعة الخاصة لفاس')<br>
                    @arabic('المدرسة العليا للهندسة')<br>
                    @arabic('والتكنولوجيا بفاس')
                </td>
            </tr>
        </table>

        <div class="sub-divider"></div>

        <!-- ===== TITLE ===== -->
        <div class="doc-title">
            <h1>Reçu d'Inscription Officiel</h1>
        </div>

        <!-- ===== BODY ===== -->
        <div class="cert-body">
            <p>Le Service de la Scolarité de l'Université Privée de Fès certifie que l'étudiant(e) mentionné(e) ci-dessous est officiellement <strong>{{ $student->registration_type == 'reinscription' ? 'réinscrit(e)' : 'inscrit(e)' }}</strong> pour l'année universitaire en cours :</p>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Nom &amp; Prénom :</td>
                <td class="info-value blue">{{ strtoupper($student->user->name) }}</td>
            </tr>
            <tr>
                <td class="info-label">Code d'Inscription (CNE) :</td>
                <td class="info-value blue">{{ $student->student_number ?? 'En cours d\'attribution' }}</td>
            </tr>
            <tr>
                <td class="info-label">N° de C.I.N :</td>
                <td class="info-value">{{ $student->cin ?? 'Non renseigné' }}</td>
            </tr>
            <tr>
                <td class="info-label">Filière d'Études :</td>
                <td class="info-value blue">{{ $student->filiere->name ?? ($student->group->filiere->name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">Niveau d'Études :</td>
                <td class="info-value">{{ $student->group?->level ?? '1' }}ère Année</td>
            </tr>
            <tr>
                <td class="info-label">Groupe d'Affectation :</td>
                <td class="info-value">{{ $student->group?->name ?? 'Non affecté' }}</td>
            </tr>
            <tr>
                <td class="info-label">Année Académique :</td>
                <td class="info-value">{{ $student->academicYear?->name ?? \App\Models\Setting::first()->academic_year ?? '2025/2026' }}</td>
            </tr>
            <tr>
                <td class="info-label">Statut Administratif :</td>
                <td class="info-value pink">INSCRIPTION DÉFINITIVE APPROUVÉE</td>
            </tr>
        </table>

        <div class="cert-body" style="margin-top: 8px;">
            <p>Ce document est généré informatiquement et atteste du traitement administratif du dossier d'inscription. Il permet à l'étudiant d'accéder aux cours et aux services de l'établissement pour la durée de l'année académique.</p>
        </div>

        <!-- ===== FOOTER / SIGNATURE ===== -->
        <div class="footer-section">
            <table class="sig-table">
                <tr>
                    <!-- QR Code left -->
                    <td class="qr-cell">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($verifyUrl) }}"
                             alt="QR Code" width="80" height="80">
                        <p>Scannez pour vérifier l'authenticité</p>
                        <p style="font-size:6.5px; color:#999; margin-top:2px;">Réf : RECU-{{ $student->id }}-{{ date('Y') }}</p>
                    </td>

                    <!-- Signature + Stamp right -->
                    <td class="sig-cell">
                        <div class="sig-date">Fès, le {{ now()->format('d/m/Y') }}</div>
                        <div class="sig-title">Le Service de la Scolarité</div>

                        <!-- Stamp Circle -->
                        <div class="stamp-wrapper">
                            <div class="stamp-circle">
                                <div class="stamp-inner">
                                    <div class="stamp-top">UNIVERSITE PRIVEE DE FES</div>
                                    <div class="stamp-logo">★ UPF ★</div>
                                    <div class="stamp-name">SCOLARITÉ</div>
                                    <div class="stamp-arabic">@arabic('الجامعة الخاصة لفاس')</div>
                                </div>
                            </div>
                        </div>

                        <div style="font-size:9px; font-weight:bold; color:#003893; margin-top:3px;">
                            Direction Académique
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Footer bar -->
            <div class="footer-bar">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc &nbsp;|&nbsp;
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web : upf.ac.ma &nbsp;|&nbsp; Email : contact@upf.ac.ma
            </div>
        </div>

    </div><!-- /doc-border -->
</div><!-- /page-wrapper -->

</body>
</html>
