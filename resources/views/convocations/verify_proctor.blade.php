<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification Convocation Surveillance — {{ $conv->reference }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4ff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.12); padding: 40px 32px; max-width: 480px; width: 100%; }
        .badge-valid { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .header { text-align: center; margin-bottom: 28px; }
        .status-icon { font-size: 56px; margin-bottom: 12px; }
        h1 { font-size: 22px; font-weight: 900; color: #111; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: 12px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-value { font-size: 14px; font-weight: 800; color: #111; text-align: right; max-width: 60%; }
        .valid-badge { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 50px; font-size: 13px; font-weight: 900; }
        .ref { font-family: monospace; font-size: 11px; color: #6b7280; text-align: center; margin-top: 20px; }
        .footer { text-align: center; margin-top: 24px; font-size: 11px; color: #9ca3af; }
        .role-principal { background: #dbeafe; color: #1e40af; }
        .role-assistant { background: #f3f4f6; color: #374151; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="status-icon">✅</div>
            <h1>Convocation Valide</h1>
            <p class="subtitle">Surveillance d'examen — Université Privée de Fès</p>
        </div>

        <div style="background: #d1fae5; border-radius: 12px; padding: 14px 18px; text-align: center; margin-bottom: 24px;">
            <span style="color: #065f46; font-weight: 900; font-size: 14px;">✅ Document authentique vérifié</span>
        </div>

        <div style="margin-bottom: 20px;">
            <div class="info-row">
                <span class="info-label">Professeur</span>
                <span class="info-value">{{ strtoupper($conv->professor->user->name ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Module</span>
                <span class="info-value">{{ $conv->exam->module->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($conv->exam->date)->isoFormat('dddd D MMMM YYYY') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Horaire</span>
                <span class="info-value">{{ date('H:i', strtotime($conv->exam->start_time)) }} – {{ $conv->exam->end_time }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Salle</span>
                <span class="info-value">{{ $conv->exam->room->name ?? 'À confirmer' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Rôle</span>
                <span class="info-value">
                    <span class="valid-badge {{ $conv->role === 'principal' ? 'role-principal' : 'role-assistant' }}">
                        {{ $conv->role === 'principal' ? '⭐ Surveillant Principal' : 'Surveillant Assistant' }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut</span>
                <span class="info-value" style="color: #059669; font-weight: 900;">
                    {{ match($conv->status) {
                        'confirmed'  => '✅ Confirmée',
                        'downloaded' => '⬇️ Téléchargée',
                        'sent'       => '✉️ Envoyée',
                        'generated'  => '📄 Générée',
                        default      => '⏳ En attente',
                    } }}
                </span>
            </div>
        </div>

        <p class="ref">Réf : {{ $conv->reference }}</p>

        <div class="footer">
            <p>Université Privée de Fès · Service de Scolarité</p>
            <p style="margin-top: 4px;">Document vérifié le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
