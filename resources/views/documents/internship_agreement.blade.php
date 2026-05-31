<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convention de Stage — {{ $request->user->name }}</title>
    
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
            font-size: 11px;
            line-height: 1.4;
        }
        .page {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: white;
            overflow: hidden;
            box-sizing: border-box;
        }
        .border-container {
            border: 6px double #003399; /* Double royal blue border */
            padding: 15px 20px;
            position: absolute;
            top: 10mm;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            box-sizing: border-box;
            overflow: hidden;
        }
        .accent-bar {
            height: 4px;
            background: #B00D5D; /* Magenta accent line */
            margin-bottom: 12px;
        }
        .footer-section {
            position: absolute;
            bottom: 12px;
            left: 20px;
            right: 20px;
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
            padding: 15px 20px;
            height: 100%;
            position: relative;
        }
        .accent-bar {
            height: 4px;
            background: #B00D5D;
            margin-bottom: 12px;
        }
        .footer-section {
            position: absolute;
            bottom: 12px;
            left: 20px;
            right: 20px;
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
            <table style="width: 100%; border-collapse: collapse; border-bottom: 2px solid #003399; padding-bottom: 8px; margin-bottom: 15px;">
                <tr>
                    <td style="width: 55%; vertical-align: middle; text-align: left; padding-bottom: 5px;">
                        <table style="border-collapse: collapse; border: none;">
                            <tr>
                                <td style="padding-right: 10px; border: none;">
                                    <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #003399, #001A66); border-radius: 10px; color: white; font-weight: 900; font-size: 16px; line-height: 45px; text-align: center;">UPF</div>
                                </td>
                                <td style="border: none; vertical-align: middle;">
                                    <div style="font-size: 12.5px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.2;">Université Privée de Fès</div>
                                    <div style="font-size: 7.5px; color: #B00D5D; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 1px;">Excellence &amp; Innovation · Fès, Maroc</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 45%; text-align: right; vertical-align: top; padding-bottom: 5px;">
                        <div style="font-size: 10.5px; font-weight: bold; color: #003399; font-family: monospace;">N° : {{ date('Y') }}/CONV/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div style="font-size: 8.5px; color: #666; margin-top: 2px;">Date : {{ date('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>

            <!-- Title Area -->
            <div style="text-align: center; margin: 12px 0; padding: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #cbd5e1;">
                <div style="font-size: 18px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 1.5px;">Convention de Stage</div>
            </div>

            <!-- Content Area (Compact Styles) -->
            <div style="color: #1e293b; text-align: justify;">
                <div style="font-weight: bold; margin-bottom: 5px; color: #003399; font-size: 11.5px;">Entre les soussignés :</div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px;">
                    <tr>
                        <td style="width: 3%; vertical-align: top; font-weight: bold; color: #B00D5D;">1.</td>
                        <td style="padding-left: 5px; padding-bottom: 4px;"><strong>L'Université Privée de Fès (UPF)</strong>, représentée par son administration.</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; font-weight: bold; color: #B00D5D;">2.</td>
                        <td style="padding-left: 5px; padding-bottom: 4px;"><strong>L'Entreprise d'Accueil</strong> (à compléter par l'organisme d'accueil).</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; font-weight: bold; color: #B00D5D;">3.</td>
                        <td style="padding-left: 5px; padding-bottom: 4px;">
                            <strong>L'Étudiant(e) :</strong> Nom et Prénom : <strong>{{ $request->user->name }}</strong><br>
                            N° d'immatriculation : <strong>STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</strong> | 
                            Inscrit(e) au titre de l'année universitaire <strong>{{ \App\Models\Setting::first()->academic_year ?? '2025/2026' }}</strong>.
                        </td>
                    </tr>
                </table>

                <div style="border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 8px;">
                    <div style="margin-bottom: 6px;"><strong style="color: #003399;">Article 1 : Objet de la convention</strong><br>
                    La présente convention a pour objet de définir les conditions dans lesquelles l'étudiant(e) effectuera son stage au sein de l'entreprise d'accueil dans le cadre de son cursus académique à l'UPF.</div>
                    
                    <div style="margin-bottom: 6px;"><strong style="color: #003399;">Article 2 : Durée et déroulement</strong><br>
                    Le stage a pour finalité l'application pratique des connaissances théoriques acquises à l'université. Les dates exactes ainsi que le sujet d'étude de stage seront définis d'un commun accord avec l'entreprise d'accueil.</div>
                    
                    <div style="margin-bottom: 6px;"><strong style="color: #003399;">Article 3 : Encadrement et Suivi</strong><br>
                    L'étudiant(e) sera soumis(e) au règlement intérieur de l'entreprise d'accueil. Il/Elle rédigera un rapport de stage qui fera l'objet d'une évaluation par l'établissement.</div>
                    
                    @if($request->reason)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 4px; font-style: italic; margin-top: 8px; font-size: 10.5px;">
                        Note complémentaire de l'étudiant lors de la demande : {{ $request->reason }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- ===== FOOTER / SIGNATURE ===== -->
            <div class="footer-section">
                <!-- Signatures Table -->
                <table style="width: 100%; border-collapse: collapse; text-align: center; margin-bottom: 10px;">
                    <tr>
                        <td style="width: 33%; vertical-align: top; border-right: 1px dashed #e2e8f0;">
                            <div style="font-weight: bold; color: #003399; font-size: 11px; text-transform: uppercase; margin-bottom: 6px;">Pour l'UPF</div>
                            <div style="font-size: 8.5px; color: #94a3b8; margin-top: 30px;">
                                Sceau &amp; Signature Autorisé
                            </div>
                        </td>
                        <td style="width: 34%; vertical-align: top; border-right: 1px dashed #e2e8f0;">
                            <div style="font-weight: bold; color: #003399; font-size: 11px; text-transform: uppercase; margin-bottom: 6px;">Pour l'Entreprise</div>
                            <div style="font-size: 8.5px; color: #94a3b8; margin-top: 30px;">
                                Sceau &amp; Signature<br>(à compléter)
                            </div>
                        </td>
                        <td style="width: 33%; vertical-align: top;">
                            <div style="font-weight: bold; color: #003399; font-size: 11px; text-transform: uppercase; margin-bottom: 6px;">L'Étudiant(e)</div>
                            <div style="font-size: 8.5px; color: #94a3b8; margin-top: 30px;">
                                Signature précédée de la mention<br>"Lu et approuvé"
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Page Footer Bar -->
                <div style="text-align: center; font-size: 8px; color: #94a3b8; font-weight: 600; border-top: 1px solid #e2e8f0; padding-top: 8px; line-height: 1.4;">
                    Université Privée de Fès — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc<br>
                    Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Web: upf.ac.ma &nbsp;|&nbsp; Email: contact@upf.ac.ma
                </div>
            </div>
        </div>
    </div>

</body>
</html>
