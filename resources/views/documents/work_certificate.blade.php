<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Travail — {{ $request->user->name }}</title>
    
    @if(isset($isPdf) && $isPdf)
    <!-- Strict DomPDF Styling -->
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
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #ffffff;
            color: #1a1a2e;
            padding: 12mm 15mm;
            font-size: 13.5px;
            line-height: 1.5;
        }
        .page {
            width: 100%;
            height: 262mm;
            position: relative;
            background: white;
            overflow: hidden;
        }
        .border-container {
            border: 6px double #003399; /* Double royal blue border */
            padding: 20px 25px;
            height: 100%;
            position: relative;
        }
        .accent-bar {
            height: 4px;
            background: #B00D5D; /* Magenta accent line */
            margin-bottom: 20px;
        }
    </style>
    @else
    <!-- Elegant Web Browser Preview Styling -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #F0F4F8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .toolbar {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
        }
        .btn-print {
            background: #003399;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 24px rgba(0,51,153,0.35);
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(0,51,153,0.45); }
        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            padding: 12mm 15mm;
            box-shadow: 0 20px 80px rgba(0,0,0,0.15);
            border-radius: 4px;
            position: relative;
        }
        .border-container {
            border: 6px double #003399;
            padding: 20px 25px;
            height: 100%;
            position: relative;
        }
        .accent-bar {
            height: 4px;
            background: #B00D5D;
            margin-bottom: 20px;
        }
        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none !important; }
            .page { box-shadow: none; border-radius: 0; width: 100%; height: 100vh; }
        }
    </style>
    @endif
</head>
<body>

    @if(!isset($isPdf) || !$isPdf)
    <!-- Print Toolbar (Only visible in web browser preview) -->
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Télécharger en PDF
        </button>
    </div>
    @endif

    <!-- A4 Document Page -->
    <div class="page">
        <div class="border-container">
            <div class="accent-bar"></div>

            <!-- Header Grid (DomPDF-friendly borderless table) -->
            <table style="width: 100%; border-collapse: collapse; border-bottom: 2px solid #003399; padding-bottom: 12px; margin-bottom: 25px;">
                <tr>
                    <td style="width: 55%; vertical-align: middle; text-align: left; padding-bottom: 10px;">
                        <table style="border-collapse: collapse; border: none;">
                            <tr>
                                <td style="padding-right: 12px; border: none;">
                                    <div style="width: 52px; height: 52px; background: linear-gradient(135deg, #003399, #001A66); border-radius: 12px; color: white; font-weight: 900; font-size: 18px; line-height: 52px; text-align: center;">UPF</div>
                                </td>
                                <td style="border: none; vertical-align: middle;">
                                    <div style="font-size: 14px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.2;">Université Privée de Fès</div>
                                    <div style="font-size: 8.5px; color: #B00D5D; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Excellence &amp; Innovation · Fès, Maroc</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 45%; text-align: right; vertical-align: top; padding-bottom: 10px;">
                        <div style="font-size: 11px; font-weight: bold; color: #003399; font-family: monospace;">Réf : {{ date('Y') }}/ATT-TRAV/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div style="font-size: 9.5px; color: #666; margin-top: 2px;">Émis le : {{ now()->format('d F Y') }}</div>
                        <div style="display: inline-block; background: #eef2ff; border: 1px solid #c7d2fe; color: #003399; font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 2px 8px; border-radius: 20px; margin-top: 4px;">✓ Document Officiel Validé</div>
                    </td>
                </tr>
            </table>

            <!-- Title Area -->
            <div style="text-align: center; margin: 25px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 800; color: #B00D5D; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 4px;">Document Administratif Officiel</div>
                <div style="font-size: 22px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 1px;">Attestation de Travail</div>
            </div>

            <!-- Introductory Text -->
            <div style="font-size: 13.5px; line-height: 1.6; color: #1e293b; text-align: justify; margin-bottom: 20px;">
                Le Secrétariat Général de l'<strong>Université Privée de Fès (UPF)</strong> certifie par la présente que la personne dont l'identité est précisée ci-dessous est bien employée en qualité de <strong>membre du corps enseignant</strong> au sein de notre établissement.
            </div>

            <!-- Info Card (DomPDF-friendly Table) -->
            <table style="width: 100%; border-collapse: collapse; border-left: 4px solid #003399; background: #f8fafc; margin: 20px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                <tr>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; width: 32%; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Nom complet</td>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700; color: #0f172a;">Prof. {{ $request->user->name }}</td>
                </tr>
                @if($request->user->professor && $request->user->professor->department)
                <tr>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Département</td>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700; color: #0f172a;">{{ $request->user->professor->department }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Statut</td>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700; color: #0f172a;">Enseignant(e)-Chercheur(se) — Temps plein</td>
                </tr>
                <tr>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Établissement</td>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700; color: #0f172a;">Université Privée de Fès (UPF)</td>
                </tr>
                <tr>
                    <td style="padding: 10px 15px; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Année universitaire</td>
                    <td style="padding: 10px 15px; font-size: 13px; font-weight: 700; color: #0f172a;">2024 / 2025</td>
                </tr>
            </table>

            <!-- Concluding Text -->
            <div style="font-size: 13.5px; line-height: 1.6; color: #1e293b; text-align: justify; margin-top: 15px;">
                Cette attestation est délivrée à l'intéressé(e) sur sa demande, pour servir et valoir ce que de droit, et ce sans aucune autre obligation de la part de notre institution.
            </div>

            <!-- Signatures Table (Absolutely locked to page bottom in PDF mode) -->
            <table style="width: 100%; border-collapse: collapse; position: absolute; bottom: 50px; left: 25px; right: 25px;">
                <tr>
                    <td style="width: 33%; text-align: center; vertical-align: top;">
                        <div style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Signature de l'Intéressé(e)</div>
                        <div style="height: 50px;"></div>
                        <div style="font-size: 11px; font-weight: 700; color: #0f172a;">Prof. {{ $request->user->name }}</div>
                    </td>
                    <td style="width: 34%; text-align: center; vertical-align: middle;">
                        <div style="width: 90px; height: 90px; border: 2px dashed #003399; border-radius: 50%; margin: 0 auto; display: block; color: #003399; opacity: 0.5;">
                            <div style="padding-top: 25px; font-size: 9px; font-weight: 700; line-height: 1.3;">Sceau<br>Officiel<br>UPF</div>
                        </div>
                    </td>
                    <td style="width: 33%; text-align: center; vertical-align: top;">
                        <div style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Fait à Fès, le {{ now()->format('d/m/Y') }}</div>
                        <div style="height: 50px;"></div>
                        <div style="font-size: 11px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">Le Directeur des RH</div>
                    </td>
                </tr>
            </table>

            <!-- Page Footer (Absolutely locked to A4 bottom) -->
            <div style="position: absolute; bottom: 15px; left: 25px; right: 25px; text-align: center; font-size: 8.5px; color: #94a3b8; font-weight: 600; border-top: 1px solid #e2e8f0; padding-top: 8px; line-height: 1.4;">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc<br>
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Fax : +212 5 35 61 21 22 &nbsp;|&nbsp; upf.ac.ma &nbsp;|&nbsp; contact@upf.ac.ma
            </div>
        </div>
    </div>

</body>
</html>
