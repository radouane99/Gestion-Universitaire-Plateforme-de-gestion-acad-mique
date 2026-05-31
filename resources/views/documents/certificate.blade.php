<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Scolarité — {{ $request->user->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            color: #1a1a2e;
            font-size: 12.5px;
            line-height: 1.5;
        }

        /* ======================== PAGE WRAPPER ======================== */
        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            overflow: hidden;
            padding: 8mm 12mm;
            position: relative;
            background: #ffffff;
        }

        /* ======================== BORDER ======================== */
        .doc-border {
            border: 7px double #003399;
            padding: 12px 16px 10px 16px;
            height: 272mm;
            position: relative;
            overflow: hidden;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 70px;
            color: rgba(0, 51, 153, 0.035);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
            letter-spacing: 2px;
            font-family: 'DejaVu Sans', sans-serif;
        }

        /* Magenta accent bar */
        .accent-bar {
            height: 3px;
            background: #B00D5D;
            margin-bottom: 10px;
            border-radius: 2px;
        }

        /* ======================== HEADER ======================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #003399;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .hdr-left {
            width: 30%;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            color: #003399;
            line-height: 1.5;
        }
        .hdr-center {
            width: 40%;
            text-align: center;
        }
        .hdr-center img {
            height: 50px;
            display: block;
            margin: 0 auto 2px auto;
        }
        .hdr-center-name {
            font-size: 8px;
            font-weight: bold;
            color: #003399;
            letter-spacing: 0.3px;
        }
        .hdr-right {
            width: 30%;
            text-align: right;
            font-size: 7.5px;
            font-weight: bold;
            color: #B00D5D;
            line-height: 1.5;
            direction: rtl;
        }

        /* Reference box */
        .ref-box {
            text-align: right;
            font-size: 9.5px;
            margin-bottom: 8px;
        }
        .ref-box span {
            font-weight: bold;
            color: #003399;
            font-family: monospace;
        }

        /* ======================== TITLE ======================== */
        .doc-title {
            text-align: center;
            margin: 10px 0 15px 0;
            padding: 12px;
            background: #f0f4ff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .doc-title h1 {
            font-size: 20px;
            font-weight: bold;
            color: #003399;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* ======================== BODY ======================== */
        .cert-body {
            font-size: 13px;
            line-height: 1.85;
            color: #1e293b;
            text-align: justify;
            margin-top: 10px;
        }

        /* Student info table */
        .info-table {
            width: 100%;
            margin: 12px 0;
            border-collapse: collapse;
            border: 1px solid #dbeafe;
            background: #f8fafc;
        }
        .info-table td {
            padding: 6px 12px;
            font-size: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            width: 35%;
            color: #475569;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .info-value {
            font-weight: bold;
            color: #0f172a;
        }
        .info-value.blue { color: #003399; }

        /* ======================== FOOTER ======================== */
        .footer-section {
            position: absolute;
            bottom: 10px;
            left: 16px;
            right: 16px;
        }

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
            width: 40%;
            text-align: left;
            vertical-align: bottom;
        }
        .qr-cell img {
            display: block;
            border: 1px solid #cbd5e1;
            padding: 4px;
            background: white;
            border-radius: 4px;
        }
        .qr-note {
            font-size: 7px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
        }
        .sig-cell {
            width: 60%;
            text-align: right;
            vertical-align: top;
        }
        .sig-date {
            font-size: 10.5px;
            font-style: italic;
            color: #475569;
            margin-bottom: 3px;
        }
        .sig-title {
            font-size: 11px;
            font-weight: bold;
            color: #003399;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        /* Stamp */
        .stamp-circle {
            width: 95px;
            height: 95px;
            border: 2px double #003399;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background: rgba(0,51,153,0.02);
        }
        .stamp-inner {
            width: 83px;
            height: 83px;
            border: 1px solid #003399;
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
            color: #003399;
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
            color: #003399;
            letter-spacing: 1px;
        }
        .stamp-sub {
            position: absolute;
            top: 52px;
            width: 83px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #003399;
        }
        .stamp-arabic {
            position: absolute;
            bottom: 11px;
            width: 83px;
            text-align: center;
            font-size: 6.5px;
            font-weight: bold;
            color: #003399;
        }
        .sig-person {
            font-size: 9.5px;
            font-weight: bold;
            color: #003399;
            margin-top: 3px;
        }

        /* Footer bar */
        .footer-bar {
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            line-height: 1.4;
        }
        .footer-bar strong {
            color: #003399;
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
        <div class="watermark">UPF SCOLARITÉ</div>
        <div class="accent-bar"></div>

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

        <!-- Reference + Date -->
        <div class="ref-box">
            <span>N° : {{ date('Y') }}/ATT/{{ $request->id }}</span>
            &nbsp;&nbsp;&nbsp;
            <span style="font-weight:normal; color:#475569; font-family:sans-serif;">Date : {{ date('d/m/Y') }}</span>
        </div>

        <!-- ===== TITLE ===== -->
        <div class="doc-title">
            <h1>Attestation de Scolarité</h1>
        </div>

        <!-- ===== CONTENT ===== -->
        <div class="cert-body">
            <p>
                L'administration de <strong>l'Université Privée de Fès (UPF)</strong> certifie par la présente que :
            </p>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Nom &amp; Prénom :</td>
                <td class="info-value blue">{{ strtoupper($request->user->name) }}</td>
            </tr>
            @php
                $studentModel = $request->user->student ?? null;
            @endphp
            <tr>
                <td class="info-label">N° d'Immatriculation (CNE) :</td>
                <td class="info-value blue">
                    {{ $studentModel?->student_number ?? 'STU-' . $request->user->id . ($request->user->id + 200) }}
                </td>
            </tr>
            @if($studentModel)
            <tr>
                <td class="info-label">N° de C.I.N :</td>
                <td class="info-value">{{ $studentModel->cin ?? 'Non renseigné' }}</td>
            </tr>
            <tr>
                <td class="info-label">Filière d'Études :</td>
                <td class="info-value blue">
                    {{ $studentModel->group?->filiere?->name ?? 'Non renseigné' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">Niveau d'Études :</td>
                <td class="info-value">{{ $studentModel->group?->level ?? '1' }}ème Année</td>
            </tr>
            <tr>
                <td class="info-label">Année Académique :</td>
                <td class="info-value">{{ $studentModel->academicYear?->name ?? '2025/2026' }}</td>
            </tr>
            @endif
        </table>

        <div class="cert-body">
            <p>
                Est régulièrement inscrit(e) au sein de notre établissement au titre de l'année universitaire en cours
                et poursuit ses études conformément au règlement pédagogique en vigueur.
            </p>
            <p style="margin-top:8px;">
                Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.
            </p>
        </div>

        <!-- ===== FOOTER / SIGNATURE ===== -->
        <div class="footer-section">
            <table class="sig-table">
                <tr>
                    <!-- QR Code -->
                    <td class="qr-cell">
                        @if(isset($qrCode) && $qrCode)
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="80" height="80">
                            <div class="qr-note">Document Numérique Officiel — Scannez pour vérifier</div>
                        @endif
                    </td>

                    <!-- Signature & Stamp -->
                    <td class="sig-cell">
                        <div class="sig-date">Fès, le {{ date('d/m/Y') }}</div>
                        <div class="sig-title">Le Secrétaire Général</div>

                        <div class="stamp-circle">
                            <div class="stamp-inner">
                                <div class="stamp-top">UNIVERSITE PRIVEE DE FES</div>
                                <div class="stamp-mid">★ UPF ★</div>
                                <div class="stamp-sub">SCOLARITÉ</div>
                                <div class="stamp-arabic">الجامعة الخاصة لفاس</div>
                            </div>
                        </div>

                        @if(isset($setting) && $setting && $setting->signature_path && file_exists(public_path('storage/' . $setting->signature_path)))
                            <br>
                            <img src="{{ public_path('storage/' . $setting->signature_path) }}"
                                 style="height:50px; margin-top:4px; display:inline-block;" alt="Signature">
                        @else
                            <div class="sig-person">Le Secrétaire Général</div>
                        @endif
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
