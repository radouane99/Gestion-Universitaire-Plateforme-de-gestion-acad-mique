<!DOCTYPE html>
<html>
<head>
    <title>Réponse à votre message - UPF</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px;">
    <div style="max-w: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <h2 style="color: #1e3a8a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 0;">Université Privée de Fès</h2>
        
        <p style="color: #334155;">Bonjour <strong>{{ $originalMessage->name }}</strong>,</p>

        <p style="color: #334155; line-height: 1.6;">Suite à votre message concernant <em>"{{ $originalMessage->subject }}"</em>, voici notre réponse :</p>

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin: 20px 0; color: #1e293b; line-height: 1.6;">
            {{ nl2br(e($replyText)) }}
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1; font-size: 13px; color: #64748b;">
            <p style="margin-bottom: 5px;"><strong>Rappel de votre message :</strong></p>
            <blockquote style="margin: 0; padding-left: 10px; border-left: 3px solid #cbd5e1; font-style: italic;">
                {{ Str::limit($originalMessage->message, 150) }}
            </blockquote>
        </div>
        
        <p style="margin-top: 40px; font-size: 12px; color: #94a3b8; text-align: center;">
            Ceci est un email généré par l'administration de l'UPF. Merci de ne pas répondre directement à cet email.
        </p>
    </div>
</body>
</html>
