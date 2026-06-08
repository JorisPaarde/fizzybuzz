# Prijsplein — Laravel app

De applicatie-backend voor Prijsplein. Draait lokaal; de marketing-site blijft op GitHub Pages.

> **Volledige installatie-checklist:** [`../SETUP.md`](../SETUP.md) (API-keys, Mailgun, VPS, troubleshooting)

## Vereisten

- PHP 8.3+
- Composer
- Node.js 20+ (voor frontend assets)

## Eerste keer opzetten

```bash
cd app
composer install
cp .env.example .env   # sla over als .env al bestaat
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

## Lokaal starten

```bash
cd app
php artisan serve
```

Open **http://localhost:8000**

- Registratie: http://localhost:8000/register
- Dashboard: http://localhost:8000/dashboard

## Structuur

```
app/
├── app/Models/          # User, Product, Wholesaler, PriceSubmission, AggregatedPrice
├── app/Services/        # AnonymizationService (privacyregels)
├── database/migrations/ # databaseschema
└── resources/views/     # Blade-templates (auth, dashboard)
```

## Database

Standaard **SQLite** (`database/database.sqlite`) — geen aparte database-server nodig.

```bash
php artisan migrate --seed   # incl. groothandels (Sligro, Bidfood, …)
```

Voor productie op een VPS: zet `DB_CONNECTION=pgsql` in `.env`.

## Prijsupload (Fase 2)

- **Foto / PDF** → OpenAI leest prijzen uit → review-scherm → opslaan
- **Handmatig** → zelf invoeren → review-scherm → opslaan
- **E-mail** → stuur bijlage naar `INBOUND_EMAIL_ADDRESS` (Mailgun webhook)

Zet je OpenAI API-key in `.env`:

```
OPENAI_API_KEY=sk-...
```

Zonder key werken handmatige uploads wel; foto/PDF/e-mail niet.

## E-mail upload

1. Zet `INBOUND_EMAIL_ADDRESS=upload@jouwdomein.nl` in `.env`
2. Configureer Mailgun inbound route → `https://jouwdomein.nl/webhooks/inbound-email`
3. Lid stuurt vanaf geregistreerd e-mailadres een PDF/foto
4. Import verschijnt ter controle in de app (`/prices/import/upload`)

## Groothandels

Leden koppelen groothandels op `/my-wholesalers`. Deze verschijnen bovenaan bij prijsinvoer.

## Productdocumentatie

Zie [`../FUNCTIONALITEIT.md`](../FUNCTIONALITEIT.md) voor de volledige functionele scope.
