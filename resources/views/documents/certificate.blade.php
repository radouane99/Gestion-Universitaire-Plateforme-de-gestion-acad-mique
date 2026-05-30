<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Scolarité — {{ $request->user->name }}</title>
    
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
            height: 250mm;
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
                        <div style="font-size: 11px; font-weight: bold; color: #003399; font-family: monospace;">N° : {{ date('Y') }}/REQ/{{ $request->id }}</div>
                        <div style="font-size: 9.5px; color: #666; margin-top: 2px;">Date : {{ date('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>

            <!-- Title Area -->
            <div style="text-align: center; margin: 25px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #cbd5e1;">
                <div style="font-size: 22px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 1.5px;">Attestation de Scolarité</div>
            </div>

            <!-- Content -->
            <div style="font-size: 14.5px; line-height: 1.8; color: #1e293b; text-align: justify; margin-top: 20px;">
                L'administration de <strong>l'Université Privée de Fès (UPF)</strong> certifie par la présente que :
                <br><br>
                L'étudiant(e) : <strong>{{ $request->user->name }}</strong><br>
                Inscrit(e) sous le numéro d'immatriculation : <strong>STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</strong><br>
                <br>
                Poursuit ses études au sein de notre établissement au titre de l'année universitaire en cours.
                <br><br>
                Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.
            </div>

            <!-- Signatures Table (Absolutely locked to page bottom in PDF mode) -->
            <table style="width: 100%; border-collapse: collapse; position: absolute; bottom: 50px; left: 25px; right: 25px; vertical-align: top;">
                <tr>
                    <td style="width: 50%; text-align: left; vertical-align: middle;">
                        @if(isset($qrCode) && $qrCode)
                        <table style="border-collapse: collapse; border: none;">
                            <tr>
                                <td style="border: none; padding-right: 10px;">
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="80" height="80" style="border: 1px solid #cbd5e1; padding: 4px; border-radius: 4px; background: white;">
                                </td>
                                <td style="border: none; vertical-align: middle;">
                                    <div style="font-size: 9px; color: #64748b; font-weight: bold; line-height: 1.3;">Document Numérique Officiel<br>Scannez pour vérifier</div>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top;">
                        <div style="font-size: 11px; font-weight: bold; color: #003399;">Fait à Fès, le {{ date('d/m/Y') }}</div>
                        <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 6px; text-transform: uppercase;">Le Secrétaire Général</div>
                        @if($setting && $setting->signature_path && file_exists(public_path('storage/' . $setting->signature_path)))
                            <img src="{{ public_path('storage/' . $setting->signature_path) }}" style="height: 60px; margin-top: 5px; display: inline-block;" alt="Signature">
                        @else
                            <div style="height: 60px; margin-top: 15px; font-size: 9px; color: #94a3b8; font-style: italic;">Signé numériquement<br>par l'administration</div>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Page Footer (Absolutely locked to A4 bottom) -->
            <div style="position: absolute; bottom: 15px; left: 25px; right: 25px; text-align: center; font-size: 8.5px; color: #94a3b8; font-weight: 600; border-top: 1px solid #e2e8f0; padding-top: 8px; line-height: 1.4;">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc<br>
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web: upf.ac.ma &nbsp;|&nbsp; Email: contact@upf.ac.ma
            </div>
        </div>
    </div>

</body>
</html>
