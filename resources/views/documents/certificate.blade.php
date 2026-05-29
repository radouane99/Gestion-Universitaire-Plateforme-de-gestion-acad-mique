<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Scolarité - {{ $setting->institution_name ?? 'UPF' }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #001A4D; margin: 0; padding: 0; }
        .page { width: 100%; position: relative; }
        .header { border-bottom: 2px solid #003399; padding-bottom: 20px; margin-bottom: 40px; }
        .header table { width: 100%; }
        .logo { font-size: 32px; font-weight: bold; color: #003399; }
        .upf-magenta { color: #B00D5D; }
        .title { text-align: center; margin-top: 50px; font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .content { margin-top: 60px; line-height: 1.8; font-size: 16px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; color: #666; border-top: 1px solid #eee; padding-top: 10px; text-align: center; }
        .stamp-area { margin-top: 80px; width: 100%; text-align: right; }
        .qr-code { position: absolute; bottom: 80px; left: 0; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <table>
                <tr>
                    <td style="width: 50%;">
                        @if(!empty($setting->logo_path))
                            <img src="{{ public_path('storage/' . $setting->logo_path) }}" style="height: 60px;" alt="Logo">
                        @else
                            <div class="logo">UPF</div>
                            <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">{{ $setting->institution_name ?? 'Université Privée de Fès' }}</div>
                        @endif
                    </td>
                    <td style="width: 50%; text-align: right; font-size: 12px; font-weight: bold;">
                        N°: {{ date('Y') }}/REQ/{{ $request->id }}<br>
                        Date: {{ date('d/m/Y') }}
                    </td>
                </tr>
            </table>
        </div>

        <h1 class="title">Attestation de Scolarité</h1>

        <div class="content">
            L'administration de <b>{{ $setting->institution_name ?? 'l\'Université Privée de Fès (UPF)' }}</b> certifie par la présente que :<br><br>
            L'étudiant(e) : <b>{{ $request->user->name }}</b><br>
            Inscrit(e) sous le numéro d'immatriculation : <b>STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</b><br>
            <br>
            Poursuit ses études au titre de l'année universitaire en cours.<br>
            Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.
        </div>

        <div class="stamp-area">
            <p style="font-weight: bold; margin-bottom: 20px;">Fait à {{ explode(' ', $setting->address ?? 'Fès')[0] }}, le {{ date('d/m/Y') }}</p>
            @if(!empty($setting->signature_path))
                <img src="{{ public_path('storage/' . $setting->signature_path) }}" style="height: 100px;" alt="Signature">
            @else
                <div style="height: 100px;">Le Secrétaire Général</div>
            @endif
        </div>

        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="80" height="80"><br>
            <span style="font-size: 8px; color: #666;">Document vérifiable</span>
        </div>

        <div class="footer">
            {{ $setting->institution_name ?? 'Université Privée de Fès' }} - {{ $setting->address ?? "Route d'Aïn Chkef, Fès - Maroc" }}<br>
            {{ $setting->phone ? 'Tél: '.$setting->phone.' | ' : '' }}Email: {{ $setting->official_email ?? 'contact@upf.ac.ma' }}
        </div>
    </div>
</body>
</html>

