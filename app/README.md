# PriceSignal — Laravel app

De applicatie-backend voor **pricesignal.nl**. Draait lokaal; de marketing-site op GitHub Pages (later pricesignal.nl).

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
- Vergelijken: http://localhost:8000/compare
- Mijn prijzen: http://localhost:8000/prices
- Groothandels: http://localhost:8000/my-wholesalers

## Structuur

```
app/
├── app/Models/          # User, Product, Wholesaler, PriceSubmission, AggregatedPrice
├── app/Services/        # AnonymizationService, PriceComparisonService, PriceImportService
├── database/migrations/ # databaseschema
└── resources/views/     # Blade-templates (auth, dashboard, compare, prices)
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

## Prijsvergelijking & inzicht (Fase 3a + 3b)

- **Gasten:** `/compare` zonder login — producten verkennen, prijzen verborgen, CTA om lid te worden
- **Leden:** zoek op productnaam op `/compare`
- Per groothandel: jouw prijs vs. anonieme marktrange (min–max)
- Vergelijking binnen je inkoopomvang-segment (klein / middel / groot)
- Minimaal 3 andere leden nodig voor marktdata (privacy)
- Filters: groothandel en periode (30/90/365 dagen)
- Dashboard (`/dashboard`): overzicht producten boven marktrange

## Productdocumentatie

- Functionele scope: [`../FUNCTIONALITEIT.md`](../FUNCTIONALITEIT.md)
- Datamodel: [`../docs/DATAMODEL.md`](../docs/DATAMODEL.md)
