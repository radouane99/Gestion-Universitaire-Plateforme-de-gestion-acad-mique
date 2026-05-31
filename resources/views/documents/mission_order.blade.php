<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordre de Mission — {{ $request->user->name }}</title>
    
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
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            color: #1a1a2e;
            font-size: 11.5px;
            line-height: 1.45;
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
            padding: 20px 25px;
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
            margin-bottom: 14px;
        }
        .footer-section {
            position: absolute;
            bottom: 12px;
            left: 25px;
            right: 25px;
        }

        /* ======================== HEADER ======================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #003399;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .hdr-left {
            width: 35%;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            color: #003399;
            line-height: 1.5;
        }
        .hdr-center {
            width: 25%;
            text-align: center;
        }
        .hdr-center img {
            height: 50px;
            display: block;
            margin: 0 auto 2px auto;
        }
        .hdr-center-name {
            font-size: 8px;
            font-weight: bold;
            color: #003399;
            letter-spacing: 0.3px;
        }
        .hdr-right {
            width: 40%;
            text-align: right;
            font-size: 7.5px;
            font-weight: bold;
            color: #B00D5D;
            line-height: 1.5;
            white-space: nowrap;
        }

        /* Reference box */
        .ref-box {
            text-align: right;
            font-size: 9.5px;
            margin-bottom: 8px;
        }
        .ref-box span {
            font-weight: bold;
            color: #003399;
            font-family: monospace;
        }

        /* Stamp */
        .stamp-circle {
            width: 95px;
            height: 95px;
            border: 2px double #003399;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background: rgba(0,51,153,0.02);
        }
        .stamp-inner {
            width: 83px;
            height: 83px;
            border: 1px solid #003399;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            left: 5px;
        }
        .stamp-top {
            position: absolute;
            top: 11px;
            width: 83px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #003399;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .stamp-mid {
            position: absolute;
            top: 30px;
            width: 83px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #003399;
            letter-spacing: 1px;
        }
        .stamp-sub {
            position: absolute;
            top: 52px;
            width: 83px;
            text-align: center;
            font-size: 5.5px;
            font-weight: bold;
            color: #003399;
        }
        .stamp-arabic {
            position: absolute;
            bottom: 11px;
            width: 83px;
            text-align: center;
            font-size: 6.5px;
            font-weight: bold;
            color: #003399;
            white-space: nowrap;
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
            margin-bottom: 18px;
        }
        .footer-section {
            position: absolute;
            bottom: 12px;
            left: 25px;
            right: 25px;
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

@php
    $logoPath = public_path('images/logo_upf.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = base64_encode(file_get_contents($logoPath));
    }
@endphp

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

            <!-- ===== HEADER ===== -->
            <table class="header-table">
                <tr>
                    <td class="hdr-left">
                        ROYAUME DU MAROC<br>
                        UNIVERSITÉ PRIVÉE DE FÈS<br>
                        École Supérieure d'Ingénierie<br>
                        et de Technologie de Fès
                    </td>
                    <td class="hdr-center">
                        @if($logoBase64)
                            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo UPF">
                        @else
                            <img src="{{ public_path('images/logo_upf.png') }}" alt="Logo UPF">
                        @endif
                        <div class="hdr-center-name">UNIVERSITÉ PRIVÉE DE FÈS</div>
                    </td>
                    <td class="hdr-right">
                        @arabic('المملكة المغربية')<br>
                        @arabic('الجامعة الخاصة لفاس')<br>
                        @arabic('المدرسة العليا للهندسة')<br>
                        @arabic('والتكنولوجيا بفاس')
                    </td>
                </tr>
            </table>

            <!-- Reference + Date + Status -->
            <div class="ref-box">
                <span>Réf : {{ date('Y') }}/ORD-MISS/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span>
                &nbsp;&nbsp;&nbsp;
                <span style="font-weight:normal; color:#475569; font-family:sans-serif;">Émis le : {{ now()->format('d/m/Y') }}</span>
                &nbsp;&nbsp;&nbsp;
                <span style="display: inline-block; background: #fff5fa; border: 1px solid #f9c0d8; color: #B00D5D; font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 1px 8px; border-radius: 20px;">✓ Ordre Officiel Validé</span>
            </div>

            <!-- Title Area -->
            <div style="text-align: center; margin: 18px 0; padding: 12px; background: #fff5fa; border-radius: 8px; border: 1px solid #f9c0d8;">
                <div style="font-size: 9px; font-weight: 800; color: #003399; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 4px;">Document Administratif Officiel</div>
                <div style="font-size: 22px; font-weight: 900; color: #B00D5D; text-transform: uppercase; letter-spacing: 1px;">Ordre de Mission</div>
            </div>

            <!-- Introductory Text -->
            <div style="font-size: 13px; line-height: 1.5; color: #1e293b; text-align: justify; margin-bottom: 15px;">
                Le Secrétariat Général de l'<strong>Université Privée de Fès (UPF)</strong> ordonne par la présente à l'enseignant(e) désigné(e) ci-dessous de se rendre dans le lieu indiqué aux dates spécifiées, afin d'accomplir la mission académique ou administrative décrite :
            </div>

            <!-- Details Table (DomPDF-friendly Table) -->
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;">
                <tr>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; background: #f8fafc; width: 32%; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Nom complet</td>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 12.5px; font-weight: 700; color: #001A4D;">Prof. {{ $request->user->name }}</td>
                </tr>
                @if($request->user->professor && $request->user->professor->department)
                <tr>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Département</td>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 12.5px; font-weight: 600; color: #001A4D;">{{ $request->user->professor->department }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Établissement</td>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 12.5px; color: #001A4D;">Université Privée de Fès (UPF)</td>
                </tr>
                <tr>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Destination</td>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 12.5px; font-weight: 700; color: #003399;">{{ $request->data['destination'] ?? 'Non précisée' }}</td>
                </tr>
                <tr>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Période de mission</td>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 12.5px; color: #001A4D;">
                        Du <strong>{{ \Carbon\Carbon::parse($request->data['start_date'] ?? now())->format('d/m/Y') }}</strong>
                        au <strong>{{ \Carbon\Carbon::parse($request->data['end_date'] ?? now())->format('d/m/Y') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Objet / Motif</td>
                    <td style="padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 12.5px; color: #001A4D;">{{ $request->data['mission_reason'] ?? 'Non précisé' }}</td>
                </tr>
            </table>

            <!-- Legal Note -->
            <div style="font-size: 11.5px; font-style: italic; color: #475569; margin: 12px 0; line-height: 1.5; border-left: 3px solid #cbd5e1; padding-left: 10px;">
                Les autorités locales de la destination et tous représentants des services publics et de la force publique sont priés de faciliter l'accomplissement de la mission de l'intéressé(e) et de lui prêter assistance en cas de besoin.
            </div>

            <!-- ===== FOOTER / SIGNATURE ===== -->
            <div class="footer-section">
                <!-- Signatures Table (Absolutely locked to page bottom in PDF mode) -->
                <table style="width: 100%; border-collapse: collapse; text-align: center; margin-bottom: 12px;">
                    <tr>
                        <td style="width: 33%; text-align: center; vertical-align: top;">
                            <div style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Signature de l'Intéressé(e)</div>
                            <div style="height: 45px;"></div>
                            <div style="font-size: 11px; font-weight: 700; color: #0f172a;">Prof. {{ $request->user->name }}</div>
                        </td>
                        <td style="width: 34%; text-align: center; vertical-align: middle;">
                            <div class="stamp-circle">
                                <div class="stamp-inner">
                                    <div class="stamp-top">UNIVERSITE PRIVEE DE FES</div>
                                    <div class="stamp-mid">★ UPF ★</div>
                                    <div class="stamp-sub">SECRETARIAT</div>
                                    <div class="stamp-arabic">@arabic('الجامعة الخاصة لفاس')</div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: top;">
                            <div style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Fait à Fès, le {{ now()->format('d/m/Y') }}</div>
                            <div style="height: 45px;"></div>
                            <div style="font-size: 11px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">Le Secrétaire Général</div>
                        </td>
                    </tr>
                </table>

                <!-- Page Footer (Absolutely locked to A4 bottom) -->
                <div style="text-align: center; font-size: 8.5px; color: #94a3b8; font-weight: 600; border-top: 1px solid #e2e8f0; padding-top: 8px; line-height: 1.4;">
                    <strong>Université Privée de Fès</strong> — Route d'Aïn Chkef, B.P. 1357, Fès 30000, Maroc<br>
                    Tél : +212 5 35 61 21 21 &nbsp;|&nbsp; upf.ac.ma &nbsp;|&nbsp; contact@upf.ac.ma
                </div>
            </div>
        </div>
    </div>

</body>
</html>
