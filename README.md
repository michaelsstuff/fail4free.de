# fail4free.de

Private Community-Website für unsere Freundesgruppe — komplett hinter einem Login und ausschließlich per Einladung zugänglich.

## Funktionen

- **Geschlossener Zugang:** Registrierung nur über zeitlich begrenzte Einladungslinks (SHA-256-gehashte Tokens, Standard-Gültigkeit 14 Tage, automatische Bereinigung per Scheduler).
- **Dashboard (`/start`):** Übersicht über Verbindungen (TeamSpeak, Discord, weitere Links mit Copy-to-Clipboard) sowie aktive Dedicated Game Server nach Spiel gruppiert (inkl. Ein-Klick-Kopieren von `host:port` und Passwort-Anzeige erst auf Klick).
- **Verschlüsselte Server-Passwörter:** Gespeichert über Laravels `encrypted`-Cast.
- **Profilverwaltung (`/profil`):** Name, E-Mail und Passwort selbst ändern.
- **Admin-Bereich (`/admin/*`):** Verwaltung von Servern, Verbindungen, Einladungen (Erstellen, Erneut senden mit Token-Rotation, Widerrufen) und Mitgliedern (Admin-Rechte & Aktiv-Status mit Schutz für den letzten aktiven Admin).
- **Web-Deploy-Endpoint (`/__deploy`):** Secret-geschützter Endpunkt für Migrationen und Erst-Admin-Seeding auf Shared Hosting ohne SSH (liefert `404`, wenn `DEPLOY_KEY` leer ist).

## Tech-Stack

- **Backend:** PHP 8.5, Laravel 13, MariaDB / MySQL
- **Frontend:** Blade, Tailwind CSS 4, Alpine.js, Vite
- **Hosting-Ziel:** Hetzner Webhosting S (Shared Hosting ohne SSH, Datei-Treiber für Session & Cache, synchrone Mails)

## Lokale Entwicklung (Docker)

Docker wird ausschließlich lokal für Entwicklung, Tests und den Produktions-Build genutzt (so entstehen `vendor/` und Assets exakt unter Linux + PHP 8.5).

### 1. Einrichtung

```powershell
# Lokale .env anlegen (gitignored)
Copy-Item .env.example .env

# Container starten (PHP 8.5 + MariaDB)
docker compose up -d --build

# Abhängigkeiten installieren, App-Key generieren und DB migrieren + Admin seeden
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

# Frontend-Assets bauen
docker compose run --rm --no-deps node sh -c "npm ci && npm run build"
```

Danach ist die Anwendung unter `http://localhost:8000` erreichbar. Mit `MAIL_MAILER=log` landen Einladungs- und Passwort-Reset-Mails lokal in `storage/logs/laravel.log`.

### 2. Tests & Code-Style

```powershell
# Feature- und Unit-Tests ausführen
docker compose exec app php artisan test

# Code-Formatierung prüfen / anwenden (Laravel Pint)
docker compose exec app ./vendor/bin/pint --test
```

## Deployment

Die vollständige Schritt-für-Schritt-Anleitung für **Hetzner Webhosting S** (konsoleH-Setup, lokaler Produktions-Build, Upload per FileZilla und Migration über `/__deploy`) steht in [`DEPLOY.md`](./DEPLOY.md).

## Secret-Management

- Produktive `.env`-Dateien und Secrets gehören **niemals** in dieses Repository.
- Im Repo liegen ausschließlich die Vorlagen [`.env.example`](./.env.example) (lokal) und [`.env.production.example`](./.env.production.example) (Hetzner-Produktion mit Platzhaltern).
