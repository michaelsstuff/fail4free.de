# Projekt-Richtlinien (`fail4free.de`)

Dieses Repository enthält die Laravel-13-Anwendung für **fail4free.de**, ausgelegt für **Hetzner Webhosting S** (Shared Hosting ohne SSH).

## Entwicklungsumgebung

- **Docker-only lokal:** PHP, Composer, Node und MariaDB laufen lokal über `compose.yaml` (`php:8.5-cli`, `mariadb:11.4`, `node:22-alpine`). Kein lokales PHP/Composer auf dem Host voraussetzen oder installieren.
- Befehle im Container ausführen:
  - PHP / Artisan / Composer: `docker compose exec app ...` (oder `docker compose run --rm --no-deps app ...`)
  - Frontend (Vite / npm): `docker compose run --rm --no-deps node sh -c "..."`
  - Tests: `docker compose exec app php artisan test`
  - Code-Style: `docker compose exec app ./vendor/bin/pint`

## Architektur- & Hosting-Vorgaben

- **PHP-Version:** `composer.json` pinnt `"platform": { "php": "8.5.0" }` passend zu konsoleH PHP 8.5.
- **Shared-Hosting-Treiber:** `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` (1 Datenbank, keine Queue-Worker).
- **Kein `config:cache` in Produktion:** Änderungen an `~/laravel/.env` per FileZilla müssen sofort wirksam bleiben; `/__deploy` führt bewusst nur `optimize:clear` und `migrate --force` aus.
- **Server-Layout:** `deploy/public_html/` enthält die produktiven Dateien für `~/public_html/` (inkl. `$app->usePublicPath(__DIR__)` und Hetzner-Symlink-Auflösung `/usr/www/users/<user>` → `/usr/home/<user>/laravel`).
- **Sprache:** Gesamte Benutzeroberfläche, Routen, E-Mails und Validierungsmeldungen sind auf **Deutsch**.

## Secret-Hygiene

- Produktions-Secrets (`.env` für Hetzner, echte `APP_KEY`, `DEPLOY_KEY`, DB- oder SMTP-Passwörter) werden **niemals** ins Repository geschrieben oder committet.
- Im Repository liegen ausschließlich `.env.example` (lokale Dummy-Werte) und `.env.production.example` (Platzhalter).
