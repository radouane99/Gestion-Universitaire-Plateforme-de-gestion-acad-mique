<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordre de Mission — {{ $request->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', Arial, sans-serif; background: #F0F4F8; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 40px 20px; }

        .toolbar { position: fixed; top: 24px; right: 24px; z-index: 9999; }
        .btn-print { background: #003399; color: white; border: none; padding: 14px 28px; border-radius: 14px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,51,153,0.35); transition: all 0.2s; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 1px; }
        .btn-print:hover { transform: translateY(-2px); }

        .page { width: 210mm; min-height: 297mm; background: white; box-shadow: 0 20px 80px rgba(0,0,0,0.15); border-radius: 4px; overflow: hidden; position: relative; }
        .page-bar { height: 8px; background: linear-gradient(90deg, #003399 0%, #B00D5D 100%); }
        .page-inner { padding: 16mm 20mm 20mm 20mm; position: relative; z-index: 1; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 80px; font-weight: 900; color: rgba(0,51,153,0.03); pointer-events: none; user-select: none; white-space: nowrap; z-index: 0; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 2px solid #003399; }
        .logo-block { display: flex; align-items: center; gap: 14px; }
        .logo-badge { width: 56px; height: 56px; background: linear-gradient(135deg, #003399, #001A66); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 20px; }
        .logo-name { font-size: 14px; font-weight: 900; color: #003399; text-transform: uppercase; letter-spacing: 1px; }
        .logo-sub { font-size: 9px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }
        .ref-block { text-align: right; }
        .ref { font-size: 11px; font-weight: 700; color: #003399; }
        .ref-date { font-size: 10px; color: #888; margin-top: 4px; }
        .badge { display: inline-block; background: #fff5fa; border: 1px solid #f9c0d8; color: #B00D5D; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 3px 10px; border-radius: 20px; margin-top: 6px; }

        .doc-title-area { text-align: center; margin: 28px 0 20px; padding: 20px; background: linear-gradient(135deg, #fff5fa, #fce8f0); border-radius: 12px; border: 1px solid #f9c0d8; }
        .doc-type { font-size: 9px; font-weight: 800; color: #003399; text-transform: uppercase; letter-spacing: 4px; margin-bottom: 8px; }
        .doc-title { font-size: 26px; font-weight: 900; color: #B00D5D; text-transform: uppercase; letter-spacing: 2px; }

        .intro { font-size: 13.5px; line-height: 1.8; color: #1a1a2e; margin-bottom: 20px; text-align: justify; }
        .intro strong { color: #003399; }

        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; border-radius: 10px; overflow: hidden; box-shadow: 0 0 0 1px #dde4ff; }
        .details-table tr:first-child td { border-top: none; }
        .details-table td { padding: 13px 16px; border: 1px solid #e8eeff; font-size: 13px; }
        .details-table td.lbl { font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #555; background: #f4f7ff; width: 32%; vertical-align: top; }
        .details-table td.val { font-weight: 600; color: #001A4D; }
        .details-table td.val strong { color: #003399; font-weight: 900; }

        .legal { font-size: 12px; font-style: italic; color: #666; margin: 16px 0; line-height: 1.7; border-left: 3px solid #e0e8ff; padding-left: 14px; }

        .signatures { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 36px; padding-top: 20px; }
        .sig-block { text-align: center; }
        .sig-title { font-size: 10px; font-weight: 800; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .sig-line { width: 150px; height: 1px; background: #ccc; margin: 55px auto 8px; }
        .sig-name { font-size: 11px; font-weight: 700; color: #001A4D; }
        .stamp-circle { width: 100px; height: 100px; border: 2px dashed #003399; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 9px; font-weight: 700; color: #003399; opacity: 0.4; margin: 0 auto; }

        .footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #eee; text-align: center; font-size: 9px; color: #aaa; font-weight: 600; }
        .footer strong { color: #003399; }

        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none !important; }
            .page { box-shadow: none; width: 100%; }
            .page-bar, .doc-title-area, .details-table td.lbl { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Télécharger en PDF
        </button>
    </div>

    <div class="page">
        <div class="page-bar"></div>
        <div class="watermark">UPF</div>

        <div class="page-inner">
            <!-- Header -->
            <div class="header">
                <div class="logo-block">
                    <div class="logo-badge">UPF</div>
                    <div>
                        <div class="logo-name">Université Privée de Fès</div>
                        <div class="logo-sub">Excellence &amp; Innovation · Fès, Maroc</div>
                    </div>
                </div>
                <div class="ref-block">
                    <div class="ref">Réf : {{ date('Y') }}/ORD-MISS/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div class="ref-date">Émis le : {{ now()->format('d F Y') }}</div>
                    <span class="badge">✓ Ordre Officiel Validé</span>
                </div>
            </div>

            <!-- Title -->
            <div class="doc-title-area">
                <div class="doc-type">Document Administratif Officiel</div>
                <div class="doc-title">Ordre de Mission</div>
            </div>

            <!-- Intro -->
            <div class="intro">
                Le Secrétariat Général de l'<strong>Université Privée de Fès (UPF)</strong> ordonne par la présente à l'enseignant(e) désigné(e) ci-dessous de se rendre dans le lieu indiqué aux dates spécifiées, afin d'accomplir la mission académique ou administrative décrite :
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    <td class="lbl">Nom complet</td>
                    <td class="val"><strong>{{ $request->user->name }}</strong></td>
                </tr>
                @if($request->user->professor && $request->user->professor->department)
                <tr>
                    <td class="lbl">Département</td>
                    <td class="val">{{ $request->user->professor->department }}</td>
                </tr>
                @endif
                <tr>
                    <td class="lbl">Établissement</td>
                    <td class="val">Université Privée de Fès (UPF)</td>
                </tr>
                <tr>
                    <td class="lbl">Destination</td>
                    <td class="val"><strong>{{ $request->data['destination'] ?? 'Non précisée' }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Période de mission</td>
                    <td class="val">
                        Du <strong>{{ \Carbon\Carbon::parse($request->data['start_date'] ?? now())->format('d/m/Y') }}</strong>
                        au <strong>{{ \Carbon\Carbon::parse($request->data['end_date'] ?? now())->format('d/m/Y') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Objet / Motif</td>
                    <td class="val">{{ $request->data['mission_reason'] ?? 'Non précisé' }}</td>
                </tr>
            </table>

            <!-- Legal note -->
            <div class="legal">
                Les autorités locales de la destination et tous représentants des services publics et de la force publique sont priés de faciliter l'accomplissement de la mission de l'intéressé(e) et de lui prêter assistance en cas de besoin.
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
                    <div class="sig-name">Le Secrétaire Général</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc<br>
                Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; upf.ac.ma &nbsp;|&nbsp; contact@upf.ac.ma
            </div>
        </div>
    </div>

</body>
</html>

