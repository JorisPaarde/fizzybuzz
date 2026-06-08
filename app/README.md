# Prijsplein — Laravel app

De applicatie-backend voor Prijsplein. Draait lokaal; de marketing-site blijft op GitHub Pages.

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

Voor productie op een VPS: zet `DB_CONNECTION=pgsql` in `.env`.

## Productdocumentatie

Zie [`../FUNCTIONALITEIT.md`](../FUNCTIONALITEIT.md) voor de volledige functionele scope.
