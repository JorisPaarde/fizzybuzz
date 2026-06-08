# Prijsplein — Installatie & setup

Stap-voor-stap handleiding om het project lokaal werkend te krijgen, inclusief API-keys en optionele diensten.

> **Productscope:** [`FUNCTIONALITEIT.md`](FUNCTIONALITEIT.md)  
> **Repo:** https://github.com/JorisPaarde/fizzybuzz  
> **Hoofdbranch:** `block3_joris`

---

## Overzicht

| Onderdeel | Wat | Waar draait het | Vereist voor basis |
|---|---|---|---|
| **Marketing** | Landingspagina | GitHub Pages / `npm start` | Nee (al live) |
| **Laravel app** | Registratie, upload, dashboard | `app/` → localhost:8000 | **Ja** |
| **OpenAI** | Foto/PDF/e-mail extractie | Externe API | Optioneel* |
| **Mailgun** | E-mail upload webhook | Externe dienst | Optioneel |
| **SQLite** | Lokale database | `app/database/` | Ja (standaard) |

\* Zonder OpenAI-key werken handmatige prijsinvoer en de rest van de app wel; alleen AI-extractie niet.

---

## Master checklist

Gebruik deze checklist van boven naar beneden.

### 1. Systeemvereisten

- [ ] **Git** geïnstalleerd
- [ ] **PHP 8.3+** met extensies: `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `curl`, `fileinfo`, `zip`
- [ ] **Composer 2.x**
- [ ] **Node.js 20+** en npm
- [ ] *(Optioneel)* **Python 3.12+** — alleen voor databron-onderzoek (`scripts/public-data-probe/`)

**Controleren (macOS/Linux):**

```bash
php -v          # ≥ 8.3
composer -V
node -v         # ≥ 20
npm -v
php -m | grep -i sqlite
```

**macOS (Homebrew):**

```bash
brew install php@8.3 composer node
```

**Ubuntu/Debian:**

```bash
sudo apt update
sudo apt install php8.3 php8.3-sqlite3 php8.3-mbstring php8.3-curl php8.3-zip php8.3-xml unzip
# Composer: https://getcomposer.org/download/
# Node: https://nodejs.org/ of via nvm
```

---

### 2. Repository ophalen

- [ ] Repo gekloond
- [ ] Juiste branch actief (`block3_joris` of feature-branch)

```bash
git clone https://github.com/JorisPaarde/fizzybuzz.git
cd fizzybuzz
git checkout block3_joris
```

---

### 3. Laravel app — basisinstallatie

- [ ] Dependencies geïnstalleerd
- [ ] `.env` aangemaakt
- [ ] App key gegenereerd
- [ ] SQLite database aangemaakt
- [ ] Migraties + seeders gedraaid
- [ ] Frontend assets gebouwd
- [ ] Server gestart

```bash
cd app

composer install
cp .env.example .env          # overslaan als .env al bestaat
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed      # incl. groothandels (Sligro, Bidfood, …)

npm install
npm run build

php artisan serve
```

- [ ] App bereikbaar op **http://localhost:8000**
- [ ] Registratie werkt: http://localhost:8000/register
- [ ] Dashboard na login: http://localhost:8000/dashboard

**Health check:**

```bash
curl -s http://localhost:8000/up
# Verwacht: {"status":"ok"} of vergelijkbaar
```

---

### 4. Tests draaien (verificatie)

- [ ] Alle tests slagen

```bash
cd app
php artisan test
```

Verwacht: **33 tests passed**.

---

### 5. API-keys & externe diensten

Kopieer `app/.env.example` naar `app/.env` en vul onderstaande velden in.

#### 5a. OpenAI — foto/PDF extractie

| Variabele | Verplicht | Waar te halen |
|---|---|---|
| `OPENAI_API_KEY` | Voor AI-upload | https://platform.openai.com/api-keys |

```env
OPENAI_API_KEY=sk-proj-...
```

- [ ] Key aangemaakt op OpenAI Platform
- [ ] Key in `app/.env` gezet (nooit committen)
- [ ] Billing/credits actief op OpenAI-account
- [ ] Getest: upload een foto/PDF via http://localhost:8000/prices/import/upload

**Model in code:** `gpt-4o-mini` (via `PriceExtractionService`).

**Zonder key:** handmatige invoer op `/prices/manual/create` werkt gewoon.

---

#### 5b. Mailgun — e-mail upload (optioneel)

| Variabele | Verplicht | Beschrijving |
|---|---|---|
| `INBOUND_EMAIL_ADDRESS` | Ja* | Adres waar leden naartoe mailen |
| `MAILGUN_WEBHOOK_SIGNING_KEY` | Productie | Webhook-handtekening |

\* Alleen nodig als je e-mail-upload wilt gebruiken.

```env
INBOUND_EMAIL_ADDRESS=upload@jouwdomein.nl
MAILGUN_WEBHOOK_SIGNING_KEY=key-...
```

**Mailgun instellen:**

1. [ ] Mailgun-account: https://www.mailgun.com/
2. [ ] Domein geverifieerd (DNS: SPF, DKIM, MX)
3. [ ] Inbound route: e-mails naar `upload@jouwdomein.nl` forwarden naar webhook
4. [ ] Webhook URL: `https://jouwdomein.nl/webhooks/inbound-email`
5. [ ] Signing key gekopieerd naar `.env`

**Lokaal testen zonder Mailgun:**

- In `local` omgeving accepteert de webhook requests **zonder** signing key (zie `InboundEmailWebhookController`).
- Simuleer met curl:

```bash
curl -X POST http://localhost:8000/webhooks/inbound-email \
  -F "sender=jan@horeca.nl" \
  -F "from=jan@horeca.nl" \
  -F "subject=Factuur" \
  -F "attachment-1=@/pad/naar/factuur.pdf"
```

> De afzender moet overeenkomen met een geregistreerd gebruikers-e-mailadres.

---

#### 5c. Overige `.env` variabelen (meestal standaard OK)

| Variabele | Standaard | Wanneer aanpassen |
|---|---|---|
| `APP_URL` | `http://localhost:8000` | Bij andere poort of productie-URL |
| `APP_ENV` | `local` | `production` op VPS |
| `APP_DEBUG` | `true` | `false` op productie |
| `DB_CONNECTION` | `sqlite` | `pgsql` op VPS |
| `MAIL_MAILER` | `log` | SMTP/Resend voor echte e-mails |
| `QUEUE_CONNECTION` | `database` | Zelfde; start worker voor async jobs |

---

### 6. Functies verifiëren in de browser

Na login als testgebruiker:

| Functie | URL | Checklist |
|---|---|---|
| Dashboard | `/dashboard` | [ ] Zichtbaar na login |
| Prijsupload (foto/PDF) | `/prices/import/upload` | [ ] Upload + review (met OpenAI key) |
| Handmatige invoer | `/prices/manual/create` | [ ] Werkt zonder OpenAI |
| Mijn prijzen | `/prices` | [ ] Lijst + bewerken/verwijderen |
| Groothandels koppelen | `/my-wholesalers` | [ ] Sligro, Bidfood, etc. selecteerbaar |
| Profiel | `/profile` | [ ] Bedrijfsgegevens aanpasbaar |

---

### 7. Marketing site (optioneel, lokaal)

De live site draait op GitHub Pages — geen server nodig.

**Lokaal previewen:**

```bash
# Vanuit repo-root
npm start
```

- [ ] Site op http://localhost:3000
- [ ] Live versie: https://jorispaarde.github.io/fizzybuzz/

**Deploy:** automatisch bij push naar `block3_joris` (alleen `index.html` + `css/`).

---

### 8. Publieke databron-probe (optioneel)

Onderzoekstool voor externe prijsdata zonder groothandel-accounts.

```bash
cd scripts/public-data-probe   # alleen als deze map in je branch staat
pip install -r requirements.txt
python3 probe.py
```

- [ ] Rapport in `scripts/public-data-probe/results/latest.json`
- [ ] Geen API-keys nodig

---

### 9. Productie (VPS) — later

Nog niet geïmplementeerd; voorbereid in architectuur.

| Stap | Actie | Status |
|---|---|---|
| VPS | Hetzner (of vergelijkbaar), Ubuntu 22.04+ | [ ] |
| Webserver | Nginx + PHP-FPM 8.3 | [ ] |
| Database | PostgreSQL, `DB_CONNECTION=pgsql` | [ ] |
| SSL | Let's Encrypt (Certbot) | [ ] |
| Domein | `APP_URL=https://app.jouwdomein.nl` | [ ] |
| OpenAI | Zelfde `OPENAI_API_KEY` | [ ] |
| Mailgun | Productie webhook + signing key | [ ] |
| Queue worker | `php artisan queue:work` (systemd/supervisor) | [ ] |
| Scheduler | `* * * * * php artisan schedule:run` | [ ] |

**Productie `.env` aanpassingen:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.jouwdomein.nl

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=prijsplein
DB_USERNAME=prijsplein
DB_PASSWORD=...

MAILGUN_WEBHOOK_SIGNING_KEY=key-...
```

---

## Snelle referentie — `.env` minimale setup

**Alleen lokaal ontwikkelen (handmatige uploads):**

```env
APP_NAME=Prijsplein
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

# Optioneel — voor foto/PDF:
# OPENAI_API_KEY=sk-proj-...
```

**Volledig (AI + e-mail):**

```env
OPENAI_API_KEY=sk-proj-...
INBOUND_EMAIL_ADDRESS=upload@jouwdomein.nl
MAILGUN_WEBHOOK_SIGNING_KEY=key-...
```

---

## Problemen oplossen

### `php artisan migrate` faalt

```bash
# Controleer of SQLite-bestand bestaat en schrijfbaar is
ls -la app/database/database.sqlite
touch app/database/database.sqlite
php artisan migrate:fresh --seed
```

### `Class "..." not found` na git pull

```bash
cd app
composer install
php artisan config:clear
php artisan cache:clear
```

### OpenAI: "API-key ontbreekt"

- [ ] `OPENAI_API_KEY` in `app/.env` (niet repo-root)
- [ ] `php artisan config:clear` na wijziging

### Foto/PDF upload geeft lege extractie

- Gescande PDF's zonder tekstlaag → upload als foto
- Controleer OpenAI credits en logs: `app/storage/logs/laravel.log`

### E-mail webhook 403

- Lokaal: laat `MAILGUN_WEBHOOK_SIGNING_KEY` leeg
- Productie: key moet exact overeenkomen met Mailgun dashboard

### `npm run build` faalt

```bash
cd app
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Poort 8000 bezet

```bash
php artisan serve --port=8001
# Pas APP_URL aan in .env
```

---

## Dagelijkse workflow

```bash
cd app
php artisan serve          # Terminal 1 — app

# Optioneel bij frontend-wijzigingen:
npm run dev                # Terminal 2 — Vite hot reload
```

---

## Links

| Resource | URL |
|---|---|
| Live marketing | https://jorispaarde.github.io/fizzybuzz/ |
| OpenAI API keys | https://platform.openai.com/api-keys |
| Mailgun | https://www.mailgun.com/ |
| Laravel docs | https://laravel.com/docs |
| Productdocumentatie | [`FUNCTIONALITEIT.md`](FUNCTIONALITEIT.md) |
| App-specifieke notes | [`app/README.md`](app/README.md) |

---

*Laatste update: juni 2026*
