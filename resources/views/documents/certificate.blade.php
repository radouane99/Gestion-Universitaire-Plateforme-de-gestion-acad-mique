<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Scolarité - UPF</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #001A4D; margin: 0; padding: 0; }
        .page { width: 210mm; height: 297mm; padding: 20mm; margin: auto; border: 1px solid #eee; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #003399; padding-bottom: 20px; }
        .logo { font-size: 32px; font-weight: 900; color: #003399; }
        .upf-magenta { color: #B00D5D; }
        .title { text-align: center; margin-top: 50px; font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; }
        .content { margin-top: 60px; line-height: 1.8; font-size: 16px; }
        .footer { position: absolute; bottom: 40px; left: 40px; right: 40px; font-size: 10px; color: #666; border-top: 1px solid #eee; padding-top: 20px; text-align: center; }
        .stamp-area { margin-top: 80px; display: flex; justify-content: flex-end; }
        .stamp { width: 150px; height: 150px; border: 2px dashed #003399; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; text-align: center; color: #003399; opacity: 0.5; }
        
        @media print {
            .print\:hidden { display: none !important; }
            .page { border: none; padding: 0; margin: 0; width: auto; height: auto; }
            body { background: white; }
        }
    </style>
</head>
<body>
    <!-- Print Button Widget -->
    <div class="print:hidden" style="position: fixed; top: 30px; right: 30px; z-index: 9999;">
        <button onclick="window.print()" style="background-color: #003399; color: white; padding: 14px 28px; border: none; border-radius: 16px; font-weight: 800; cursor: pointer; font-size: 14px; box-shadow: 0 10px 25px -5px rgba(0, 51, 153, 0.4); display: flex; align-items: center; gap: 10px; transition: transform 0.2s; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimer l'Attestation
        </button>
    </div>

    <div class="page">
        <div class="header">
            <div>
                <div class="logo">UPF</div>
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Université Privée de Fès</div>
            </div>
            <div style="text-align: right; font-size: 12px; font-weight: bold;">
                N°: {{ date('Y') }}/REQ/{{ $request->id }}<br>
                Date: {{ date('d/m/Y') }}
            </div>
        </div>

        <h1 class="title">Attestation de Scolarité</h1>

        <div class="content">
            L'administration de l'<b>Université Privée de Fès (UPF)</b> certifie par la présente que :<br><br>
            L'étudiant(e) : <b>{{ $request->user->name }}</b><br>
            Inscrit(e) sous le numéro d'immatriculation : <b>STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</b><br>
            <br>
            Poursuit ses études à l'UPF au titre de l'année universitaire <b>2024 / 2025</b>.<br>
            Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.
        </div>

        <div class="stamp-area">
            <div style="text-align: center;">
                <p style="font-weight: bold; margin-bottom: 20px;">Fait à Fès, le {{ date('d/m/Y') }}</p>
                <div class="stamp">
                    <br><br>Sceau de <br> l'Institution
                </div>
                <p style="font-size: 12px; font-weight: bold; margin-top: 10px;">Le Secrétaire Général</p>
            </div>
        </div>

        <div class="footer">
            Université Privée de Fès - Route d'Aïn Chkef, Fès - Maroc<br>
            Tél: +212 5 35 61 21 21 | Web: upf.ac.ma | Email: contact@upf.ac.ma
        </div>
    </div>
</body>
</html>
