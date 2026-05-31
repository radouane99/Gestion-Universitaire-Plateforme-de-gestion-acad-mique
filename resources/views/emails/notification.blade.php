<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notification UPF' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F0F4F8; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#F0F4F8; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #003399 0%, #001A66 100%); border-radius: 16px 16px 0 0; padding: 32px 40px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <!-- Logo UPF text -->
                                        <div style="display:inline-block; background:rgba(255,255,255,0.15); border-radius:12px; padding:10px 20px; margin-bottom:16px;">
                                            <span style="color:#ffffff; font-size:22px; font-weight:900; letter-spacing:2px;">UPF</span>
                                        </div>
                                        <br>
                                        <span style="color:rgba(255,255,255,0.75); font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase;">
                                            Université Privée de Fès
                                        </span>
                                        <br>
                                        <span style="color:rgba(255,255,255,0.5); font-size:9px; letter-spacing:1px;">
                                            Portail Académique Officiel
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ICON + TITLE BAND -->
                    <tr>
                        <td style="background:#B00D5D; padding: 16px 40px; text-align:center;">
                            <span style="color:#ffffff; font-size:13px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase;">
                                {{ $icon ?? '🔔' }} &nbsp; {{ $title ?? 'Nouvelle Notification' }}
                            </span>
                        </td>
                    </tr>

                    <!-- BODY CARD -->
                    <tr>
                        <td style="background:#ffffff; padding: 40px 40px 32px 40px; border-left:1px solid #E2E8F0; border-right:1px solid #E2E8F0;">

                            <!-- Greeting -->
                            <p style="margin:0 0 20px 0; font-size:16px; font-weight:700; color:#0F172A;">
                                Bonjour {{ $recipientName ?? 'Étudiant(e)' }},
                            </p>

                            <!-- Body message -->
                            <p style="margin:0 0 28px 0; font-size:14px; color:#475569; line-height:1.7;">
                                {{ $body ?? '' }}
                            </p>

                            <!-- Info box if present -->
                            @if(isset($infoBox))
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td style="background:#F0F4FF; border-left:4px solid #003399; border-radius:0 8px 8px 0; padding:16px 20px;">
                                        <p style="margin:0; font-size:13px; color:#003399; font-weight:700;">{{ $infoBox }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- CTA Button -->
                            @if(isset($actionUrl))
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom:8px;">
                                        <a href="{{ $actionUrl }}"
                                           style="display:inline-block; background:linear-gradient(135deg,#003399,#001A66); color:#ffffff; font-size:13px; font-weight:800; text-decoration:none; padding:14px 36px; border-radius:10px; letter-spacing:1px; text-transform:uppercase;">
                                            {{ $actionText ?? 'Accéder à mon espace' }} →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                        </td>
                    </tr>

                    <!-- DISCLAIMER -->
                    <tr>
                        <td style="background:#F8FAFC; padding:20px 40px; border:1px solid #E2E8F0; border-top:none;">
                            <p style="margin:0; font-size:11px; color:#94A3B8; line-height:1.6; text-align:center;">
                                Cet email a été envoyé automatiquement par le portail académique de l'<strong style="color:#64748B;">Université Privée de Fès</strong>.<br>
                                Merci de ne pas répondre à cet email. Pour toute question, connectez-vous à votre espace étudiant.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#003399; border-radius:0 0 16px 16px; padding:20px 40px; text-align:center;">
                            <p style="margin:0; color:rgba(255,255,255,0.6); font-size:10px; letter-spacing:0.5px;">
                                <strong style="color:rgba(255,255,255,0.9);">Université Privée de Fès</strong> &nbsp;·&nbsp;
                                Route d'Aïn Chkef, Fès 30000, Maroc &nbsp;·&nbsp;
                                <a href="https://upf.ac.ma" style="color:rgba(255,255,255,0.7); text-decoration:none;">upf.ac.ma</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
