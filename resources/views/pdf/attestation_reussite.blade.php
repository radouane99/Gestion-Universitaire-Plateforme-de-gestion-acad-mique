<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation de Réussite - {{ $student->student_number }}</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
        }
        .border-container {
            border: 4px double #1e3a8a;
            padding: 40px;
            height: 90%;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .sub-logo {
            font-size: 14px;
            font-weight: bold;
            color: #db2777;
            margin-top: 5px;
        }
        .title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #0f172a;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 10px;
            display: inline-block;
        }
        .content {
            font-size: 18px;
            line-height: 1.8;
            text-align: justify;
            margin-bottom: 50px;
        }
        .student-name {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
        }
        .highlight {
            font-weight: bold;
            color: #0f172a;
        }
        .footer {
            width: 100%;
            margin-top: 60px;
        }
        .footer table {
            width: 100%;
            border: none;
        }
        .footer td {
            border: none;
            width: 50%;
            text-align: center;
            font-size: 16px;
        }
        .qr-code {
            position: absolute;
            bottom: 40px;
            left: 40px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(30, 58, 138, 0.05);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <div class="border-container">
        <div class="watermark">UPF OFFICIEL</div>

        <div class="header">
            <div class="logo-text">UNIVERSITÉ DE PORTFOLIO (UPF)</div>
            <div class="sub-logo">FACULTÉ DES SCIENCES & DE L'ÉDUCATION</div>
        </div>

        <div style="text-align: center;">
            <div class="title">Attestation de Réussite</div>
        </div>

        <div class="content">
            <p>Le Doyen de la Faculté des Sciences & de l'Éducation atteste par la présente que l'étudiant(e) :</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <span class="student-name">{{ $student->user->name }}</span>
            </p>
            
            <p>
                Inscrit(e) sous le numéro Apogée (CNE) <span class="highlight">{{ $student->student_number }}</span> et titulaire de la CIN <span class="highlight">{{ $student->cin ?? '___________' }}</span>, a été déclaré(e) <span class="highlight">{{ $studentData['annual_decision'] }}</span> au titre de l'année universitaire <span class="highlight">{{ $academicYear->name }}</span>.
            </p>

            <p>
                Niveau d'études : <span class="highlight">{{ $student->group->level }}ème Année</span><br>
                Filière : <span class="highlight">{{ $student->group->filiere->name }}</span><br>
                Moyenne générale : <span class="highlight">{{ number_format($studentData['annual_average'], 2, ',', ' ') }} / 20</span><br>
                Mention : <span class="highlight">{{ $mention }}</span>
            </p>

            <p style="margin-top: 30px; font-size: 14px;">
                <em>En foi de quoi, cette attestation lui est délivrée pour servir et valoir ce que de droit.</em>
            </p>
        </div>

        <div class="footer">
            <table>
                <tr>
                    <td></td>
                    <td>
                        <p>Fait à Rabat, le {{ now()->format('d/m/Y') }}</p>
                        <p class="highlight">Le Doyen de la Faculté</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="100" height="100">
            <div style="font-size: 10px; color: #64748b; margin-top: 5px; text-align: center; width: 100px;">
                Scanner pour<br>vérifier l'authenticité
            </div>
        </div>
    </div>

</body>
</html>
