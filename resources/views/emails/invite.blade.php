<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Einladung zu fail4free.de</title>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #e2e8f0; padding: 32px;">
    <div style="max-width: 520px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 28px; border: 1px solid #334155;">
        <h1 style="margin-top: 0; font-size: 20px; color: #ffffff;">Einladung zu fail4free.de</h1>
        <p style="line-height: 1.6; color: #cbd5e1;">
            Hallo! Du wurdest zur privaten Community-Seite <strong>fail4free.de</strong> eingeladen.
        </p>
        <p style="margin: 24px 0;">
            <a href="{{ $acceptUrl }}"
               style="display: inline-block; background: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: 600;">
                Konto jetzt erstellen
            </a>
        </p>
        <p style="font-size: 13px; color: #94a3b8; line-height: 1.5;">
            Dieser persönliche Link ist gültig bis <strong>{{ $invite->expires_at->format('d.m.Y H:i') }} Uhr</strong>
            und kann nur einmal verwendet werden.
        </p>
        <p style="font-size: 12px; color: #64748b; word-break: break-all;">
            Falls der Button nicht funktioniert, öffne diesen Link direkt im Browser:<br>
            {{ $acceptUrl }}
        </p>
    </div>
</body>
</html>
