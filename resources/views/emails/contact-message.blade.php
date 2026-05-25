<!DOCTYPE html>
<html>
<head>
    <title>Nouveau Message de Contact</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px;">
    <div style="max-w: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <h2 style="color: #1e3a8a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 0;">Nouveau Message via UPF Contact</h2>
        
        <p style="color: #334155;">Vous avez reçu une nouvelle demande de contact depuis le site de l'Université Privée de Fès.</p>

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;"><strong>Expéditeur :</strong> {{ $contactMessage->name }}</p>
            <p style="margin: 0 0 10px 0;"><strong>Email :</strong> <a href="mailto:{{ $contactMessage->email }}" style="color: #2563eb;">{{ $contactMessage->email }}</a></p>
            <p style="margin: 0 0 0 0;"><strong>Sujet :</strong> {{ $contactMessage->subject }}</p>
        </div>
        
        <div style="background-color: #fff1f2; padding: 20px; border-left: 4px solid #db2777; margin-top: 20px; border-radius: 4px;">
            <p style="margin: 0; color: #1e293b; line-height: 1.6;">{{ nl2br(e($contactMessage->message)) }}</p>
        </div>
        
        <p style="margin-top: 40px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            Cet email a été généré automatiquement. Vous pouvez voir tous les messages dans la boîte de réception de votre tableau de bord.
        </p>
    </div>
</body>
</html>
