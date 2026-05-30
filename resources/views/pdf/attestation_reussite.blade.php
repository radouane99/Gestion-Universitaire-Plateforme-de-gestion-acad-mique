<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation de Réussite — {{ $student->student_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Serif', Georgia, serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 12px;
            line-height: 1.45;
        }

        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            overflow: hidden;
            padding: 8mm 12mm;
            position: relative;
            background: #fffdf8;
        }

        .doc-border {
            border: 8px double #c9a227;
            padding: 12px 16px 10px 16px;
            height: 272mm;
            position: relative;
            overflow: hidden;
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
            width: 30%;
            font-size: 7.5px;
            font-weight: bold;
            color: #003893;
            line-height: 1.5;
            font-family: 'DejaVu Sans', sans-serif;
        }
        .hdr-center {
            width: 40%;
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
            font-family: 'DejaVu Sans', sans-serif;
        }
        .hdr-right {
            width: 30%;
            text-align: right;
            font-size: 7.5px;
            font-weight: bold;
            color: #9b1d6e;
            line-height: 1.5;
            direction: rtl;
            font-family: 'DejaVu Sans', sans-serif;
        }

        /* Gradient divider */
        .divider {
            height: 2px;
            background: #c9a227;
            border-radius: 1px;
            margin-bottom: 10px;
            opacity: 0.6;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 75px;
            color: rgba(201,162,39,0.04);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
            font-family: 'DejaVu Sans', sans-serif;
        }

        /* ======================== TITLE ======================== */
        .doc-title {
            text-align: center;
            margin: 8px 0 10px 0;
        }
        .doc-title h1 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #003893;
            text-transform: uppercase;
            border-bottom: 2px solid #9b1d6e;
            display: inline-block;
            padding-bottom: 3px;
        }

        /* ======================== CONTENT ======================== */
        .content {
            font-size: 12.5px;
            line-height: 1.65;
            text-align: justify;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .student-name {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            color: #9b1d6e;
            text-align: center;
            display: block;
            margin: 5px 0;
        }
        .highlight { font-weight: bold; color: #003893; }
        .highlight-pink { font-weight: bold; color: #9b1d6e; }

        /* Info grid */
        .info-grid {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
            background: rgba(201,162,39,0.03);
            border: 1px solid rgba(201,162,39,0.2);
            position: relative;
            z-index: 1;
        }
        .info-grid td {
            padding: 5px 10px;
            font-size: 11.5px;
            border-bottom: 1px solid rgba(201,162,39,0.1);
        }
        .info-grid tr:last-child td { border-bottom: none; }
        .ig-label {
            font-weight: bold;
            width: 25%;
            color: #475569;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .ig-value { font-weight: bold; color: #0f172a; }
        .ig-value.blue { color: #003893; }
        .ig-value.pink { color: #9b1d6e; }

        /* ======================== FOOTER ======================== */
        .footer-section {
            position: absolute;
            bottom: 10px;
            left: 16px;
            right: 16px;
            z-index: 2;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .sig-table td { vertical-align: bottom; padding: 0; }

        .qr-cell {
            width: 42%;
            text-align: left;
            vertical-align: bottom;
        }
        .qr-cell img {
            border: 1px solid #c9a227;
            padding: 3px;
            background: white;
            border-radius: 4px;
            display: block;
        }
        .qr-note {
            font-size: 7px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .sig-cell {
            width: 58%;
            text-align: right;
            vertical-align: top;
        }
        .sig-date {
            font-style: italic;
            font-size: 10px;
            color: #475569;
            margin-bottom: 3px;
        }
        .sig-title {
            font-weight: bold;
            font-size: 10.5px;
            color: #003893;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        /* Stamp */
        .stamp-circle {
            width: 90px;
            height: 90px;
            border: 2px double #003893;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background: rgba(0,56,147,0.02);
        }
        .stamp-inner {
            width: 78px;
            height: 78px;
            border: 1px solid #003893;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            left: 5px;
        }
        .stamp-top {
            position: absolute; top: 11px; width: 78px;
            text-align: center; font-size: 5.5px;
            font-weight: bold; color: #003893;
            text-transform: uppercase; letter-spacing: 0.2px;
        }
        .stamp-mid {
            position: absolute; top: 29px; width: 78px;
            text-align: center; font-size: 13px;
            font-weight: bold; color: #003893; letter-spacing: 1px;
        }
        .stamp-sub {
            position: absolute; top: 51px; width: 78px;
            text-align: center; font-size: 5.5px;
            font-weight: bold; color: #003893;
        }
        .stamp-arabic {
            position: absolute; bottom: 11px; width: 78px;
            text-align: center; font-size: 6.5px;
            font-weight: bold; color: #003893;
        }
        .sig-person {
            font-size: 9.5px; font-weight: bold;
            color: #003893; margin-top: 3px;
        }

        /* Footer bar */
        .footer-bar {
            border-top: 1px solid rgba(201,162,39,0.4);
            padding-top: 4px;
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            font-family: 'DejaVu Sans', sans-serif;
        }
        .footer-bar strong { color: #003893; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="doc-border">
        <div class="watermark">UPF OFFICIEL</div>

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
                    <img src="{{ public_path('images/logo_upf.png') }}" alt="Logo UPF">
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

        <div class="divider"></div>

        <!-- ===== TITLE ===== -->
        <div class="doc-title">
            <h1>Attestation de Réussite</h1>
        </div>

        <!-- ===== CONTENT ===== -->
        <div class="content">
            <p>
                Le Doyen de l'Université certifie, après délibération académique officielle, que l'étudiant(e) :
            </p>
            <span class="student-name">{{ $student->user->name }}</span>
            <p>
                Inscrit(e) sous le numéro d'immatriculation (CNE)
                <span class="highlight">{{ $student->student_number }}</span>
                et titulaire de la CIN
                <span class="highlight">{{ $student->cin ?? '___________' }}</span>,
                a été déclaré(e)
                <span class="highlight">{{ $studentData['annual_decision'] }}</span>
                au titre de l'année universitaire
                <span class="highlight">{{ $academicYear->name }}</span>.
            </p>
        </div>

        <table class="info-grid">
            <tr>
                <td class="ig-label">Niveau d'études :</td>
                <td class="ig-value">{{ $student->group->level }}ème Année</td>
                <td class="ig-label">Filière d'Étude :</td>
                <td class="ig-value blue">{{ $student->group->filiere->name }}</td>
            </tr>
            <tr>
                <td class="ig-label">Moyenne Générale :</td>
                <td class="ig-value"><span class="highlight">{{ number_format($studentData['annual_average'], 2, ',', ' ') }}</span> / 20</td>
                <td class="ig-label">Mention Officielle :</td>
                <td class="ig-value pink">{{ $mention }}</td>
            </tr>
        </table>

        <div class="content" style="margin-top:8px;">
            <p style="font-style:italic; font-size:11px; color:#475569;">
                En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.
            </p>
        </div>

        <!-- ===== FOOTER ===== -->
        <div class="footer-section">
            <table class="sig-table">
                <tr>
                    <td class="qr-cell">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="80" height="80">
                        <div class="qr-note">Authentification officielle UPF — Scanner pour vérifier</div>
                    </td>
                    <td class="sig-cell">
                        <div class="sig-date">Fès, le {{ now()->format('d/m/Y') }}</div>
                        <div class="sig-title">Le Doyen de la Faculté</div>

                        <!-- Stamp -->
                        <div class="stamp-circle">
                            <div class="stamp-inner">
                                <div class="stamp-top">UNIVERSITE PRIVEE DE FES</div>
                                <div class="stamp-mid">★ UPF ★</div>
                                <div class="stamp-sub">SCOLARITÉ</div>
                                <div class="stamp-arabic">الجامعة الخاصة لفاس</div>
                            </div>
                        </div>

                        <div class="sig-person">Le Doyen</div>
                    </td>
                </tr>
            </table>

            <div class="footer-bar">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc &nbsp;|&nbsp;
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web : upf.ac.ma &nbsp;|&nbsp; Email : contact@upf.ac.ma
            </div>
        </div>

    </div>
</div>

</body>
</html>
