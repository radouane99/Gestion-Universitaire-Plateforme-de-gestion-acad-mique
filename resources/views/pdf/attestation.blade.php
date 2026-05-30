<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Réussite — {{ $student->student_number }}</title>
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
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            background: #ffffff;
            color: #1e293b;
            padding: 12mm 15mm;
            font-size: 13px;
            line-height: 1.5;
        }
        .page {
            width: 100%;
            height: 270mm;
            position: relative;
            background: #fffdf9; /* Slight warm paper tint */
            overflow: hidden;
        }
        .border-container {
            border: 8px double #d4af37; /* Double royal gold border */
            padding: 25px 30px;
            height: 100%;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.4);
            padding-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
            width: 33%;
        }
        .header-left {
            text-align: left;
            font-size: 9.5px;
            font-weight: bold;
            color: #003893;
            line-height: 1.4;
            font-family: sans-serif;
        }
        .header-center {
            text-align: center;
        }
        .header-center img {
            height: 60px;
            margin-bottom: 5px;
        }
        .header-right {
            text-align: right;
            font-size: 9.5px;
            font-weight: bold;
            color: #b50060;
            line-height: 1.4;
            font-family: sans-serif;
        }
        .title-container {
            text-align: center;
            margin: 20px 0;
        }
        .title-container h1 {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #003893;
            margin: 0;
            text-transform: uppercase;
            border-bottom: 2px solid #b50060;
            display: inline-block;
            padding-bottom: 5px;
        }
        .certificate-body {
            margin: 20px 0;
            text-align: justify;
            font-size: 14px;
            line-height: 1.7;
        }
        .highlight {
            font-weight: bold;
            color: #003893;
        }
        .info-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
            background: rgba(212, 175, 55, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        .info-table td {
            padding: 8px 15px;
            font-size: 13.5px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }
        .info-label {
            font-weight: bold;
            width: 38%;
            color: #475569;
            font-family: sans-serif;
            font-size: 11px;
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
            bottom: 40px;
            left: 30px;
            right: 30px;
        }
        .qr-code-box {
            text-align: left;
            width: 40%;
            vertical-align: bottom;
        }
        .qr-code-box img {
            border: 1px solid #d4af37;
            padding: 4px;
            background-color: #fff;
            border-radius: 6px;
        }
        .qr-code-box p {
            font-size: 8px;
            color: #64748b;
            margin-top: 5px;
            font-weight: bold;
            font-family: sans-serif;
        }
        .signature-box {
            text-align: right;
            width: 60%;
            vertical-align: top;
            font-size: 12px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #003893;
            text-decoration: underline;
        }
        .signature-date {
            font-style: italic;
            margin-bottom: 8px;
            color: #475569;
        }
        .signature-image {
            height: 50px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="border-container">
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

        <!-- Body -->
        <div class="certificate-body">
            <p style="margin-bottom: 10px;">Le Président de l'Université Privée de Fès certifie que l'étudiant(e) :</p>
            
            <table class="info-table">
                <tr>
                    <td class="info-label">Nom &amp; Prénom :</td>
                    <td class="info-value">{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Code d'Inscription (Matricule) :</td>
                    <td class="info-value highlight">{{ $student->student_number }}</td>
                </tr>
                <tr>
                    <td class="info-label">N° de C.I.N :</td>
                    <td class="info-value">{{ $student->cin }}</td>
                </tr>
                <tr>
                    <td class="info-label">Date &amp; Lieu de Naissance :</td>
                    <td class="info-value">
                        {{ $student->birth_date ? $student->birth_date->format('d/m/Y') : 'N/A' }} à {{ $student->birth_place ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Filière d'Études :</td>
                    <td class="info-value highlight">{{ $student->filiere->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Niveau Validé :</td>
                    <td class="info-value">{{ $student->group?->level ?? 'Licence 1' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Année Académique :</td>
                    <td class="info-value">{{ $student->academicYear?->name ?? '2025/2026' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Moyenne Générale Obtenue :</td>
                    <td class="info-value"><span class="highlight">{{ number_format($gpa, 2) }}</span> / 20</td>
                </tr>
                <tr>
                    <td class="info-label">Mention Attribuée :</td>
                    <td class="info-value highlight" style="color: #b50060;">{{ $mention }}</td>
                </tr>
            </table>

            <p style="margin-top: 15px;">En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
        </div>

        <!-- Footer / Signatures -->
        <table class="footer-table">
            <tr>
                <td class="qr-code-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode($verifyUrl) }}" alt="QR Code Verification">
                    <p>Scannez pour vérifier l'authenticité</p>
                </td>
                <td class="signature-box">
                    <div class="signature-date">Fès, le {{ now()->format('d/m/Y') }}</div>
                    <div class="signature-title">Le Président de l'Université Privée de Fès</div>
                    <img src="https://i.imgur.com/uFomf2Y.png" class="signature-image" alt="Signature & Cachet" onerror="this.style.display='none'">
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
