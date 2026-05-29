<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convention de Stage - UPF</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #001A4D; margin: 0; padding: 0; }
        .page { width: 210mm; min-height: 297mm; padding: 20mm; margin: auto; border: 1px solid #eee; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #003399; padding-bottom: 20px; }
        .logo { font-size: 32px; font-weight: 900; color: #003399; }
        .title { text-align: center; margin-top: 40px; font-size: 24px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; }
        .content { margin-top: 40px; line-height: 1.6; font-size: 14px; text-align: justify; }
        .footer { position: absolute; bottom: 40px; left: 40px; right: 40px; font-size: 10px; color: #666; border-top: 1px solid #eee; padding-top: 20px; text-align: center; }
        .signatures { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 30%; }
        
        @media print {
            .print\:hidden { display: none !important; }
            .page { border: none; padding: 0; margin: 0; width: auto; height: auto; }
            body { background: white; }
        }
    </style>
</head>
<body>
    <div class="print:hidden" style="position: fixed; top: 30px; right: 30px; z-index: 9999;">
        <button onclick="window.print()" style="background-color: #003399; color: white; padding: 14px 28px; border: none; border-radius: 16px; font-weight: 800; cursor: pointer; font-size: 14px; box-shadow: 0 10px 25px -5px rgba(0, 51, 153, 0.4); display: flex; align-items: center; gap: 10px; transition: transform 0.2s; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimer la Convention
        </button>
    </div>

    <div class="page">
        <div class="header">
            <div>
                <div class="logo">UPF</div>
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Université Privée de Fès</div>
            </div>
            <div style="text-align: right; font-size: 12px; font-weight: bold;">
                N°: {{ date('Y') }}/CONV/{{ $request->id }}<br>
                Date: {{ date('d/m/Y') }}
            </div>
        </div>

        <h1 class="title">Convention de Stage</h1>

        <div class="content">
            <b>Entre les soussignés :</b><br><br>
            <b>1. L'Université Privée de Fès (UPF)</b>, représentée par son administration,<br>
            <br>
            <b>2. L'Entreprise d'Accueil</b> (à compléter par l'entreprise),<br>
            <br>
            <b>3. L'Étudiant(e) :</b><br>
            Nom et Prénom : <b>{{ $request->user->name }}</b><br>
            N° d'immatriculation : <b>STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</b><br>
            Inscrit(e) au titre de l'année universitaire <b>2024 / 2025</b>.<br>
            <br><br>
            <b>Article 1 : Objet de la convention</b><br>
            La présente convention a pour objet de définir les conditions dans lesquelles l'étudiant(e) effectuera un stage au sein de l'entreprise d'accueil dans le cadre de sa formation à l'UPF.
            <br><br>
            <b>Article 2 : Durée et déroulement</b><br>
            Le stage a pour finalité l'application pratique des connaissances théoriques acquises à l'université. Les dates exactes ainsi que le sujet du stage seront définis en accord avec l'entreprise d'accueil.
            <br><br>
            <b>Article 3 : Encadrement et Suivi</b><br>
            L'étudiant(e) sera soumis(e) au règlement intérieur de l'entreprise d'accueil. Il/Elle rédigera un rapport de stage qui sera évalué par l'établissement.
            <br><br>
            @if($request->reason)
                <i>Note de l'étudiant lors de la demande : {{ $request->reason }}</i>
            @endif
        </div>

        <div class="signatures">
            <div class="signature-box">
                <p style="font-weight: bold;">Pour l'UPF</p>
                <div style="margin-top: 10px; font-size: 10px; color: #999;">
                    <br><br><br>Sceau et Signature
                </div>
            </div>
            <div class="signature-box">
                <p style="font-weight: bold;">Pour l'Entreprise</p>
                <div style="margin-top: 10px; font-size: 10px; color: #999;">
                    <br><br><br>Sceau et Signature<br>(à compléter)
                </div>
            </div>
            <div class="signature-box">
                <p style="font-weight: bold;">L'Étudiant(e)</p>
                <div style="margin-top: 10px; font-size: 10px; color: #999;">
                    <br><br><br>Signature précédée de la mention "Lu et approuvé"
                </div>
            </div>
        </div>

        <div class="footer">
            Université Privée de Fès - Route d'Aïn Chkef, Fès - Maroc<br>
            Tél: +212 5 35 61 21 21 | Web: upf.ac.ma | Email: contact@upf.ac.ma
        </div>
    </div>
</body>
</html>

