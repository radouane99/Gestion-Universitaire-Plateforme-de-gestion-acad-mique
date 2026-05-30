<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation de Réussite — {{ $student->student_number }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            color: #1e293b;
            background-color: #ffffff;
            padding: 10mm 12mm;
            font-size: 12.5px;
            line-height: 1.45;
        }
        .page {
            width: 100%;
            height: 190mm;
            position: relative;
            background-color: #fcfbf7; /* Slight warm paper tint */
            overflow: hidden;
        }
        .border-container {
            border: 8px double #d4af37; /* Double royal gold border */
            padding: 20px 25px;
            height: 100%;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.4);
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: top;
            width: 33%;
        }
        .header-left {
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            color: #003893;
            line-height: 1.3;
            font-family: sans-serif;
        }
        .header-center {
            text-align: center;
        }
        .header-center img {
            height: 50px;
        }
        .header-right {
            text-align: right;
            font-size: 9px;
            font-weight: bold;
            color: #b50060;
            line-height: 1.3;
            font-family: sans-serif;
        }
        .title-container {
            text-align: center;
            margin: 10px 0 15px 0;
        }
        .title-container h1 {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #003893;
            margin: 0;
            text-transform: uppercase;
            border-bottom: 2px solid #b50060;
            display: inline-block;
            padding-bottom: 3px;
        }
        .content {
            font-size: 13.5px;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 15px;
        }
        .student-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #b50060;
        }
        .highlight {
            font-weight: bold;
            color: #003893;
        }
        .info-grid {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
            background: rgba(212, 175, 55, 0.02);
            border: 1px solid rgba(212, 175, 55, 0.15);
        }
        .info-grid td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .info-label {
            font-weight: bold;
            width: 25%;
            color: #475569;
            font-family: sans-serif;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-weight: bold;
            color: #0f172a;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            position: absolute;
            bottom: 20px;
            left: 25px;
            right: 25px;
        }
        .qr-code-box {
            text-align: left;
            width: 50%;
            vertical-align: bottom;
        }
        .qr-code-box img {
            border: 1px solid #d4af37;
            padding: 3px;
            background-color: #fff;
            border-radius: 4px;
        }
        .qr-code-box p {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
            font-family: sans-serif;
        }
        .signature-box {
            text-align: right;
            width: 50%;
            vertical-align: top;
            font-size: 11.5px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #003893;
            text-decoration: underline;
        }
        .signature-date {
            font-style: italic;
            margin-bottom: 5px;
            color: #475569;
        }
        .signature-image {
            height: 45px;
            opacity: 0.85;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            color: rgba(212, 175, 55, 0.03);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
            font-family: sans-serif;
        }
    </style>
</head>
<body>

    <div class="page">
        <div class="border-container">
            <div class="watermark">UPF OFFICIEL</div>

            <!-- Header Table -->
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        ROYAUME DU MAROC<br>
                        UNIVERSITÉ PRIVÉE DE FÈS<br>
                        École Supérieure d'Ingénierie<br>
                        et de Technologie de Fès
                    </td>
                    <td class="header-center">
                        <img src="{{ public_path('images/logo_upf.png') }}" alt="Logo UPF" onerror="this.src='https://www.upf.ac.ma/images/logo_upf.png'">
                    </td>
                    <td class="header-right" dir="rtl">
                        المملكة المغربية<br>
                        الجامعة الخاصة لفاس<br>
                        المدرسة العليا للهندسة<br>
                        والتكنولوجيا بفاس
                    </td>
                </tr>
            </table>

            <!-- Title -->
            <div class="title-container">
                <h1>Attestation de Réussite</h1>
            </div>

            <!-- Content Body -->
            <div class="content">
                <p style="margin-bottom: 5px;">Le Doyen de l'Université certifie, après délibération académique officielle, que l'étudiant(e) :</p>
                
                <p style="text-align: center; margin: 8px 0;">
                    <span class="student-name">{{ $student->user->name }}</span>
                </p>
                
                <p style="margin-bottom: 10px;">
                    Inscrit(e) sous le numéro d'immatriculation (CNE) <span class="highlight">{{ $student->student_number }}</span> et titulaire de la CIN <span class="highlight">{{ $student->cin ?? '___________' }}</span>, a été déclaré(e) <span class="highlight">{{ $studentData['annual_decision'] }}</span> au titre de l'année universitaire <span class="highlight">{{ $academicYear->name }}</span>.
                </p>

                <table class="info-grid">
                    <tr>
                        <td class="info-label">Niveau d'études :</td>
                        <td class="info-value">{{ $student->group->level }}ème Année</td>
                        <td class="info-label">Filière d'Étude :</td>
                        <td class="info-value highlight">{{ $student->group->filiere->name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Moyenne Générale :</td>
                        <td class="info-value"><span class="highlight">{{ number_format($studentData['annual_average'], 2, ',', ' ') }}</span> / 20</td>
                        <td class="info-label">Mention Officielle :</td>
                        <td class="info-value highlight" style="color: #b50060;">{{ $mention }}</td>
                    </tr>
                </table>

                <p style="margin-top: 8px; font-style: italic; font-size: 11.5px; color: #475569;">
                    En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.
                </p>
            </div>

            <!-- Footer / Signatures -->
            <table class="footer-table">
                <tr>
                    <td class="qr-code-box">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="80" height="80">
                        <p>Authentification officielle UPF - Scanner pour vérifier</p>
                    </td>
                    <td class="signature-box">
                        <div class="signature-date">Fès, le {{ now()->format('d/m/Y') }}</div>
                        <div class="signature-title">Le Doyen de la Faculté</div>
                        <img src="https://i.imgur.com/uFomf2Y.png" class="signature-image" alt="Signature & Cachet" onerror="this.style.display='none'">
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
