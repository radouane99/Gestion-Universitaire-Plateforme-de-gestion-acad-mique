<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Réussite - {{ $student->student_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
            background-color: #fff;
        }

        .border-container {
            border: 8px double #003893;
            padding: 30px;
            position: relative;
            height: 94%;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .header-table td {
            vertical-align: top;
            width: 33%;
        }

        .header-left {
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #003893;
        }

        .header-center {
            text-align: center;
        }

        .header-center img {
            height: 75px;
            margin-bottom: 5px;
        }

        .header-right {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #b50060;
        }

        .title-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 40px;
        }

        .title-container h1 {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #003893;
            margin: 0;
            text-transform: uppercase;
            border-bottom: 2px solid #b50060;
            display: inline-block;
            padding-bottom: 5px;
        }

        .certificate-body {
            margin-top: 30px;
            margin-bottom: 40px;
            text-align: justify;
        }

        .certificate-body p {
            margin-bottom: 18px;
            font-size: 14.5px;
        }

        .highlight {
            font-weight: bold;
            color: #003893;
        }

        .info-table {
            width: 100%;
            margin: 25px 0;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 0;
            font-size: 15px;
        }

        .info-label {
            font-weight: bold;
            width: 35%;
            color: #475569;
        }

        .info-value {
            font-weight: 900;
            color: #0f172a;
        }

        .footer-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: top;
        }

        .qr-code-box {
            text-align: left;
            width: 40%;
        }

        .qr-code-box img {
            border: 1px solid #cbd5e1;
            padding: 6px;
            background-color: #fff;
            border-radius: 8px;
        }

        .qr-code-box p {
            font-size: 9px;
            color: #64748b;
            margin-top: 5px;
            font-weight: bold;
        }

        .signature-box {
            text-align: right;
            width: 60%;
            font-size: 13px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #003893;
            text-decoration: underline;
        }

        .signature-date {
            font-style: italic;
            margin-bottom: 15px;
            color: #475569;
        }

        .signature-image {
            height: 60px;
            opacity: 0.85;
        }
    </style>
</head>
<body>

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
                <!-- Fallback local logo if remote/public doesn't load -->
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
        <p>Le Président de l'Université Privée de Fès certifie que l'étudiant(e) :</p>
        
        <table class="info-table">
            <tr>
                <td class="info-label">Nom & Prénom :</td>
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
                <td class="info-label">Date & Lieu de Naissance :</td>
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

        <p>En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
    </div>

    <!-- Footer / Signatures -->
    <table class="footer-table">
        <tr>
            <td class="qr-code-box">
                <!-- QR Code points directly to verification page -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($verifyUrl) }}" alt="QR Code Verification">
                <p>Scannez pour vérifier l'authenticité</p>
            </td>
            <td class="signature-box">
                <div class="signature-date">Fès, le {{ now()->format('d/m/Y') }}</div>
                <div class="signature-title">Le Président de l'Université Privée de Fès</div>
                <!-- Pre-loaded signature of the dean/president -->
                <img src="https://i.imgur.com/uFomf2Y.png" class="signature-image" alt="Signature & Cachet" onerror="this.style.display='none'">
            </td>
        </tr>
    </table>
</div>

</body>
</html>
