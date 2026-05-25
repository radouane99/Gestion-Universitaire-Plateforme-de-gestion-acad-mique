<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation de Travail — {{ $request->user->name }}</title>
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

        /* ======= PRINT BUTTON ======= */
        .toolbar {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            gap: 12px;
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

        /* ======= A4 PAGE ======= */
        .page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            padding: 0;
            box-shadow: 0 20px 80px rgba(0,0,0,0.15);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        /* Top color bar */
        .page-bar {
            height: 8px;
            background: linear-gradient(90deg, #003399 0%, #B00D5D 100%);
        }

        .page-inner { padding: 16mm 20mm 20mm 20mm; }

        /* ======= HEADER ======= */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 2px solid #003399;
            margin-bottom: 10px;
        }
        .logo-block { display: flex; align-items: center; gap: 14px; }
        .logo-badge {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #003399, #001A66);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 20px;
            letter-spacing: -1px;
        }
        .logo-text { }
        .logo-text .name { font-size: 14px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 1px; }
        .logo-text .subtitle { font-size: 9px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }

        .ref-block { text-align: right; }
        .ref-block .ref { font-size: 11px; font-weight: 700; color: #003399; }
        .ref-block .date { font-size: 10px; color: #888; margin-top: 4px; }
        .ref-block .badge {
            display: inline-block;
            background: #f0f4ff;
            border: 1px solid #d0dbff;
            color: #003399;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 6px;
        }

        /* ======= TITLE ======= */
        .doc-title-area {
            text-align: center;
            margin: 28px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8faff, #f0f4ff);
            border-radius: 12px;
            border: 1px solid #e0e8ff;
        }
        .doc-type { font-size: 9px; font-weight: 800; color: #B00D5D; text-transform: uppercase; letter-spacing: 4px; margin-bottom: 8px; }
        .doc-title { font-size: 26px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 2px; }

        /* ======= CONTENT ======= */
        .content { font-size: 14px; line-height: 2; color: #1a1a2e; text-align: justify; margin-bottom: 20px; }
        .content strong { color: #003399; }

        .info-card {
            background: #f8faff;
            border: 1px solid #d0dbff;
            border-left: 4px solid #003399;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0;
        }
        .info-card .row { display: flex; align-items: baseline; gap: 10px; margin-bottom: 6px; }
        .info-card .row:last-child { margin-bottom: 0; }
        .info-card .label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #888; min-width: 120px; }
        .info-card .value { font-size: 14px; font-weight: 700; color: #001A4D; }

        /* ======= SIGNATURE AREA ======= */
        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 24px;
        }
        .sig-block { text-align: center; }
        .sig-block .sig-title { font-size: 10px; font-weight: 800; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .sig-line { width: 160px; height: 1px; background: #ccc; margin: 60px auto 8px; }
        .sig-name { font-size: 11px; font-weight: 700; color: #001A4D; }

        .stamp-circle {
            width: 110px; height: 110px;
            border: 2px dashed #003399;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            text-align: center;
            font-size: 9px; font-weight: 700;
            color: #003399;
            opacity: 0.4;
            margin: 0 auto;
        }

        /* ======= FOOTER ======= */
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 9px;
            color: #aaa;
            font-weight: 600;
        }
        .footer strong { color: #003399; }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(0, 51, 153, 0.03);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            z-index: 0;
        }

        /* ======= PRINT ======= */
        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none !important; }
            .page { box-shadow: none; border-radius: 0; width: 100%; min-height: 100vh; }
            .page-bar { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .doc-title-area, .info-card { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <!-- Print Toolbar -->
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Télécharger en PDF
        </button>
    </div>

    <!-- A4 Document -->
    <div class="page">
        <div class="page-bar"></div>
        <div class="watermark">UPF</div>

        <div class="page-inner" style="position: relative; z-index: 1;">

            <!-- Header -->
            <div class="header">
                <div class="logo-block">
                    <div class="logo-badge">UPF</div>
                    <div class="logo-text">
                        <div class="name">Université Privée de Fès</div>
                        <div class="subtitle">Excellence &amp; Innovation · Fès, Maroc</div>
                    </div>
                </div>
                <div class="ref-block">
                    <div class="ref">Réf : {{ date('Y') }}/ATT-TRAV/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div class="date">Émis le : {{ now()->format('d F Y') }}</div>
                    <span class="badge">✓ Document Officiel Validé</span>
                </div>
            </div>

            <!-- Title -->
            <div class="doc-title-area">
                <div class="doc-type">Document Administratif Officiel</div>
                <div class="doc-title">Attestation de Travail</div>
            </div>

            <!-- Content -->
            <div class="content">
                Le Secrétariat Général de l'<strong>Université Privée de Fès (UPF)</strong> certifie par la présente que la personne dont l'identité est précisée ci-dessous est bien employée en qualité de <strong>membre du corps enseignant</strong> au sein de notre établissement.
            </div>

            <!-- Info Card -->
            <div class="info-card">
                <div class="row">
                    <span class="label">Nom complet</span>
                    <span class="value">{{ $request->user->name }}</span>
                </div>
                @if($request->user->professor && $request->user->professor->department)
                <div class="row">
                    <span class="label">Département</span>
                    <span class="value">{{ $request->user->professor->department }}</span>
                </div>
                @endif
                <div class="row">
                    <span class="label">Statut</span>
                    <span class="value">Enseignant(e)-Chercheur(se) — Temps plein</span>
                </div>
                <div class="row">
                    <span class="label">Établissement</span>
                    <span class="value">Université Privée de Fès (UPF)</span>
                </div>
                <div class="row">
                    <span class="label">Année universitaire</span>
                    <span class="value">2024 / 2025</span>
                </div>
            </div>

            <div class="content" style="margin-top: 16px;">
                Cette attestation est délivrée à l'intéressé(e) sur sa demande, pour servir et valoir ce que de droit, et ce sans aucune autre obligation de la part de notre institution.
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="sig-block">
                    <div class="sig-title">Signature de l'Intéressé(e)</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $request->user->name }}</div>
                </div>
                <div class="sig-block">
                    <div class="sig-title">Cachet de l'Institution</div>
                    <div class="stamp-circle">Sceau<br>Officiel<br>UPF</div>
                </div>
                <div class="sig-block">
                    <div class="sig-title">Fait à Fès, le {{ now()->format('d/m/Y') }}</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">Le Directeur des Ressources Humaines</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc<br>
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; Fax : +212 5 35 61 21 22 &nbsp;|&nbsp; upf.ac.ma &nbsp;|&nbsp; contact@upf.ac.ma
            </div>
        </div>
    </div>

</body>
</html>
