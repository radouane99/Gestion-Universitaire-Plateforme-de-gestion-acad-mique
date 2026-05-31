<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diplôme Officiel de Réussite — {{ $student->student_number }}</title>
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
            font-size: 13px;
            line-height: 1.5;
        }
        .page {
            width: 100%;
            height: 190mm;
            position: relative;
            background-color: #fcfbf7; /* Slight warm paper tint */
            overflow: hidden;
        }
        .border-container {
            border: 10px double #d4af37; /* Royal gold double border */
            padding: 18px 25px;
            height: 100%;
            position: relative;
        }
        .inner-frame {
            border: 1px solid rgba(212, 175, 55, 0.4);
            height: 100%;
            width: 100%;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
            width: 33%;
        }
        .header-left {
            text-align: left;
            font-size: 8.5px;
            font-weight: bold;
            color: #003893;
            line-height: 1.4;
            font-family: sans-serif;
        }
        .header-center {
            text-align: center;
        }
        .header-center img {
            height: 48px;
            margin-bottom: 2px;
        }
        .header-right {
            text-align: right;
            font-size: 8.5px;
            font-weight: bold;
            color: #b50060;
            line-height: 1.4;
            font-family: sans-serif;
        }
        .title-container {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .title-container h1 {
            font-size: 26px;
            font-weight: bold;
            color: #003893;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .title-container h2 {
            font-size: 13px;
            color: #b50060;
            margin: 3px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            font-family: sans-serif;
        }
        .diploma-body {
            text-align: center;
            line-height: 1.6;
            font-size: 13px;
            margin: 10px auto;
            max-width: 850px;
        }
        .highlight-name {
            font-size: 20px;
            font-weight: bold;
            color: #b50060;
            margin: 6px 0;
            letter-spacing: 1px;
        }
        .highlight-field {
            font-size: 15px;
            font-weight: bold;
            color: #003893;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            position: absolute;
            bottom: 15px;
            left: 20px;
            right: 20px;
        }
        .qr-code-box {
            text-align: left;
            width: 30%;
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
            margin: 3px 0 0 0;
            font-weight: bold;
            font-family: sans-serif;
        }
        .seal-box {
            text-align: center;
            width: 40%;
            vertical-align: middle;
        }
        .seal-circle {
            width: 80px;
            height: 80px;
            border: 2px dashed #d4af37;
            border-radius: 50%;
            display: inline-block;
            line-height: 75px;
            color: #d4af37;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            font-family: sans-serif;
            opacity: 0.65;
        }
        .signature-box {
            text-align: right;
            width: 30%;
            vertical-align: top;
            font-size: 11px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 3px;
            color: #003893;
            text-decoration: underline;
        }
        .signature-date {
            font-style: italic;
            margin-bottom: 5px;
            color: #475569;
        }
        .signature-image {
            height: 40px;
            opacity: 0.9;
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

<div class="page">
    <div class="border-container">
        <div class="inner-frame">
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
                        @if($logoBase64)
                            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo UPF">
                        @else
                            <img src="{{ public_path('images/logo_upf.png') }}" alt="Logo UPF" onerror="this.src='https://www.upf.ac.ma/images/logo_upf.png'">
                        @endif
                    </td>
                    <td class="header-right">
                        @arabic('المملكة المغربية')<br>
                        @arabic('الجامعة الخاصة لفاس')<br>
                        @arabic('المدرسة العليا للهندسة')<br>
                        @arabic('والتكنولوجيا بفاس')
                    </td>
                </tr>
            </table>

            <!-- Title -->
            <div class="title-container">
                <h1>Diplôme de Licence Académique</h1>
                <h2>Décerné au titre de la réussite aux examens</h2>
            </div>

            <!-- Body -->
            <div class="diploma-body">
                Le Président de l'Université Privée de Fès certifie, conformément aux procès-verbaux des délibérations
                arrêtés par le jury d'examen en date du {{ now()->format('d/m/Y') }}, que l'étudiant(e) :
                
                <div class="highlight-name">{{ $student->user->name }}</div>
                
                né(e) le {{ $student->birth_date ? $student->birth_date->format('d/m/Y') : 'N/A' }} à {{ $student->birth_place ?? 'N/A' }}<br>
                titulaire du C.I.N numéro : <strong>{{ $student->cin }}</strong> et du Code National d'Étudiant : <strong>{{ $student->student_number }}</strong><br>
                a validé avec succès l'ensemble du cursus académique requis pour l'obtention du diplôme dans la filière :
                
                <div class="highlight-field" style="margin-top: 4px;">{{ $student->filiere->name }}</div>
                
                avec la moyenne générale de : <strong>{{ number_format($gpa, 2) }} / 20</strong>, obtenant la mention officielle : <strong style="color: #b50060;">{{ $mention }}</strong>.
            </div>

            <!-- Footer / Signatures -->
            <table class="footer-table">
                <tr>
                    <td class="qr-code-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($verifyUrl) }}" alt="QR Code Verification">
                        <p>Authentification officielle UPF</p>
                    </td>
                    <td class="seal-box">
                        <div class="seal-circle">Sceau de l'UPF</div>
                    </td>
                    <td class="signature-box">
                        <div class="signature-date">Fès, le {{ now()->format('d/m/Y') }}</div>
                        <div class="signature-title">Le Président de l'Université</div>
                        <img src="https://i.imgur.com/uFomf2Y.png" class="signature-image" alt="Signature & Cachet" onerror="this.style.display='none'">
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>
