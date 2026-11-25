# Deployment-Anleitung — `fail4free.de` auf Hetzner Webhosting S

Diese Anleitung beschreibt das vollständige, manuelle Deployment per **FileZilla (SFTP/FTPS)** auf **Hetzner Webhosting S** (ohne SSH).

> **Wichtig zum Secret-Management:**
> Produktions-Secrets (`.env` für Hetzner) gehören **niemals** in dieses Git-Repository. Erzeuge und pflege die Produktions-`.env` außerhalb des Repos und lade sie separat per FileZilla hoch.

---

## Zielstruktur auf Hetzner

```text
~/                          (Home-Verzeichnis des Haupt-FTP/SFTP-Benutzers)
├── laravel/                ← App-Code, vendor/, storage/, .env (NICHT öffentlich)
│   ├── app/
│   ├── bootstrap/
│   │   └── cache/          ← leer beim Erst-Upload, schreibbar (0755/0775)
│   ├── config/
│   ├── database/
│   ├── lang/
│   ├── resources/
│   ├── routes/
│   ├── storage/            ← schreibbar (0755/0775)
│   │   ├── app/
│   │   ├── framework/
│   │   │   ├── cache/
│   │   │   ├── sessions/
│   │   │   └── views/
│   │   └── logs/
│   ├── vendor/
│   ├── artisan
│   ├── composer.json
│   ├── composer.lock
│   └── .env                ← NUR hier, manuell von dir hochgeladen
└── public_html/            ← DocumentRoot der Domain
    ├── index.php           ← aus deploy/public_html/index.php
    ├── .htaccess           ← aus deploy/public_html/.htaccess
    ├── robots.txt          ← aus deploy/public_html/robots.txt
    ├── favicon.ico         ← aus public/favicon.ico
    └── build/              ← aus public/build/ (Vite-Output)
```

---

## 1. Einmalige Einrichtung in konsoleH

1. **Domain & HTTPS:**
   - Domain `fail4free.de` aufschalten.
   - Unter *Einstellungen → SSL/TLS* Let's Encrypt aktivieren und **HTTPS-Weiterleitung erzwingen**.
2. **PHP-Version auf 8.5 setzen:**
   - Unter *Einstellungen → PHP-Konfiguration* **PHP 8.5** wählen und speichern.
3. **Datenbank anlegen:**
   - Unter *Dienste → Datenbanken* eine MariaDB/MySQL-Datenbank erstellen.
   - Host, Datenbankname, Benutzername und Passwort sicher im Passwortmanager speichern.
4. **Mailbox für Einladungs-Mails anlegen:**
   - Unter *Dienste → E-Mail* z. B. `noreply@fail4free.de` anlegen.
   - SMTP-Daten merken: Host `mail.your-server.de`, Port `587` (STARTTLS), Benutzer `noreply@fail4free.de`, Passwort.
   - SPF / DKIM in konsoleH aktivieren, damit Einladungen nicht im Spam landen.
5. **Cronjob einrichten (1 Cronjob inklusive):**
   - Unter *Einstellungen → Cronjob Manager* einen Cronjob anlegen (z. B. alle 15 Minuten):
     ```text
     /usr/bin/php85 /usr/home/<DEIN_HETZNER_USER>/laravel/artisan schedule:run
     ```
   - **Wichtig:** Explizit `/usr/bin/php85` verwenden (nicht `/usr/bin/php`), da die Standard-CLI-Version auf Shared Hosting älter sein kann.
6. **FileZilla-Zugang (Haupt-FTP-Benutzer!):**
   - In FileZilla den **Haupt-FTP-Benutzer** aus konsoleH verwenden (SFTP Port `22` oder FTPS Port `21`).
   - *Hinweis:* Zusätzliche FTP-Benutzer sind auf `public_html/` beschränkt und können den Ordner `~/laravel/` nicht anlegen.

---

## 2. Produktions-`.env` selbst erstellen (einmalig)

Erstelle diese Datei **außerhalb** des Git-Repos (z. B. in einem privaten Ordner oder direkt aus deinem Passwortmanager):

1. Kopiere [`.env.production.example`](./.env.production.example) an einen sicheren Ort außerhalb des Projektordners und benenne sie in `.env` um.
2. Erzeuge lokal in deinem eigenen Terminal zwei Zufallsschlüssel:
   - **`APP_KEY`** (über Docker PHP 8.5):
     ```powershell
     docker compose run --rm --no-deps app php artisan key:generate --show
     ```
   - **`DEPLOY_KEY`** (64-stelliges Hex-Secret in PowerShell):
     ```powershell
     -join ((1..32) | ForEach-Object { '{0:x2}' -f (Get-Random -Maximum 256) })
     ```
     *(Oder kryptografisch via PHP: `docker compose run --rm --no-deps app php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`)*
3. Trage in deiner Produktions-`.env` alle echten Werte ein:
   - `APP_KEY` (**unbedingt im Passwortmanager sichern** — Verlust macht verschlüsselte Server-Passwörter unlesbar!)
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`
   - `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` (nur für den allerersten Deploy nötig)
   - `DEPLOY_KEY`
4. Lade diese Datei per FileZilla direkt nach `~/laravel/.env` hoch.
   - **Bei späteren Deploys `~/laravel/.env` niemals überschreiben.**

---

## 3. Lokalen Produktions-Build erzeugen (im Docker-Container)

Docker wird hier nur lokal genutzt, damit `vendor/` exakt unter **Linux + PHP 8.5** entsteht.

### A. Frontend-Assets bauen (Vite)

```powershell
docker compose run --rm --no-deps node sh -c "npm ci && npm run build"
```

Das erzeugt den fertigen Produktions-Ordner `public/build/` (inkl. `manifest.json`).

### B. Produktions-`vendor/` ohne Dev-Pakete erzeugen (ohne lokales Dev-Setup kaputtzumachen)

Um lokal `phpunit` & Co. nicht zu verlieren, baue das Produktions-Paket in einem temporären Staging-Ordner `dist/` (steht in `.gitignore`):

```powershell
# 1. Alten Staging-Ordner entfernen und neu anlegen
Remove-Item -Recurse -Force -ErrorAction SilentlyContinue dist
New-Item -ItemType Directory -Force dist\laravel, dist\public_html | Out-Null

# 2. App-Dateien in dist\laravel kopieren (ohne Secrets, Git, Dev-Dateien, Caches)
$include = @('app', 'bootstrap', 'config', 'database', 'lang', 'resources', 'routes', 'artisan', 'composer.json', 'composer.lock')
foreach ($item in $include) {
    Copy-Item -Recurse -Force $item "dist\laravel\$item"
}

# 3. Lokale Bootstrap-Cache-Dateien entfernen (falls vorhanden)
Get-ChildItem -Path dist\laravel\bootstrap\cache -Filter *.php -ErrorAction SilentlyContinue | Remove-Item -Force

# 4. Leere storage-Ordnerstruktur anlegen
@(
    'dist\laravel\storage\app\private',
    'dist\laravel\storage\app\public',
    'dist\laravel\storage\framework\cache\data',
    'dist\laravel\storage\framework\sessions',
    'dist\laravel\storage\framework\views',
    'dist\laravel\storage\logs'
) | ForEach-Object { New-Item -ItemType Directory -Force $_ | Out-Null }

# 5. Produktions-Abhängigkeiten unter Linux PHP 8.5 installieren
docker compose run --rm --no-deps app composer install --no-dev --optimize-autoloader --no-interaction --working-dir=/var/www/html/dist/laravel

# 6. public_html zusammenstellen (vorgepatchte Hetzner-Templates + Vite-Build)
Copy-Item -Force deploy\public_html\index.php dist\public_html\index.php
Copy-Item -Force deploy\public_html\.htaccess dist\public_html\.htaccess
Copy-Item -Force deploy\public_html\robots.txt dist\public_html\robots.txt
Copy-Item -Force public\favicon.ico dist\public_html\favicon.ico
Copy-Item -Recurse -Force public\build dist\public_html\build
```

Prüfe kurz, dass **keine** `.env` in `dist/` liegt:

```powershell
Get-ChildItem -Recurse -Force dist -Filter ".env*"
```

*(Darf nichts ausgeben!)*

---

## 4. Upload mit FileZilla

Verbinde dich per SFTP (Haupt-FTP-Benutzer) mit deinem Hetzner-Server.

### Erst-Upload
1. Erstelle auf dem Server neben `public_html/` (also in `/usr/home/<user>/`) den Ordner `laravel/` (falls noch nicht vorhanden).
   - *Hinweis:* `public_html/` ist auf Hetzner ein Symlink auf `/usr/www/users/<user>/`. `deploy/public_html/index.php` löst deshalb automatisch auf `/usr/home/<user>/laravel` auf — bitte den Ordner `laravel/` **nicht** in `public_html/` legen.
2. Lade den Inhalt von `dist/laravel/` in `~/laravel/` hoch.
3. Lade den Inhalt von `dist/public_html/` in `~/public_html/` hoch.
4. Falls noch nicht in Schritt 2 geschehen: Lade deine privat erstellte Produktions-`.env` nach `~/laravel/.env` hoch.
5. Prüfe in FileZilla (Rechtsklick → *Dateiberechtigungen*), dass `~/laravel/storage/` (rekursiv) und `~/laravel/bootstrap/cache/` schreibbar sind (`755`, falls nötig `775`).

### Schneller Folge-Deploy (Updates)
Wenn sich `composer.lock` nicht geändert hat, musst du `vendor/` (~10 MB) nicht erneut hochladen. Synchronisiere einfach:
- `dist/laravel/app/` → `~/laravel/app/`
- `dist/laravel/config/` → `~/laravel/config/`
- `dist/laravel/database/` → `~/laravel/database/`
- `dist/laravel/lang/` → `~/laravel/lang/`
- `dist/laravel/resources/` → `~/laravel/resources/`
- `dist/laravel/routes/` → `~/laravel/routes/`
- `dist/public_html/build/` → `~/public_html/build/` (alten `build/`-Ordner vorher leeren oder komplett ersetzen)

**Niemals überschreiben:** `~/laravel/.env` und `~/laravel/storage/`.

---

## 5. Migrationen & Erst-Admin ausführen

1. Öffne im Browser: `https://fail4free.de/__deploy`
2. Gib deinen `DEPLOY_KEY` in das Formular ein und starte den Vorgang.
   - Führt automatisch `optimize:clear`, `migrate --force` und (nur wenn die `users`-Tabelle noch leer ist) `AdminUserSeeder` aus.
   - *Hinweis:* Es wird bewusst **kein** `config:cache` ausgeführt, damit Änderungen an `~/laravel/.env` per FileZilla sofort greifen und dich niemals aussperren.
3. Logge dich unter `https://fail4free.de/login` mit dem angelegten Admin-Konto ein.
4. **Wichtige Nacharbeit nach dem ersten Login:**
   - Öffne `~/laravel/.env` (z. B. über FileZilla/konsoleH) und **lösche die Zeile `ADMIN_PASSWORD=...`**.
   - Optional: Leere `DEPLOY_KEY=` zwischen Deploys, dann liefert `/__deploy` konsequent `404`.
5. Entferne lokal den temporären `dist/`-Ordner (falls gewünscht):
   ```powershell
   Remove-Item -Recurse -Force dist
   ```

---

## 6. Produktions-Checkliste nach dem ersten Upload

- [ ] `https://fail4free.de/` lädt mit gültigem HTTPS-Zertifikat, Styles & JS laden aus `/build/` ohne 404/500.
- [ ] `https://fail4free.de/laravel/.env` liefert **404**.
- [ ] `https://fail4free.de/storage/logs/laravel.log` liefert **403 oder 404** *(Hetzners Apache blockt `*.log` global mit `403` — beides ist sicher)*.
- [ ] `https://fail4free.de/__deploy` lehnt einen falschen Key mit **403** ab (und liefert **404**, wenn `DEPLOY_KEY` leer ist).
- [ ] Echte Einladungs-Mail an eine externe Adresse verschicken → Zustellung & Spam-Ordner prüfen.
- [ ] Kompletter Durchlauf: Einladung annehmen → Registrierung → Login → Dashboard → Server-Passwort auf Klick anzeigen.
- [ ] `ADMIN_PASSWORD` aus `~/laravel/.env` entfernt und `APP_KEY` sicher im Passwortmanager hinterlegt.
