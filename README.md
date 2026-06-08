# Prijsplein

Platform waar horecabedrijven inkoopprijzen bij groothandels vergelijken en samen scherpere tarieven onderhandelen.

> **Productdocumentatie:** [`FUNCTIONALITEIT.md`](FUNCTIONALITEIT.md) — lees dit eerst.

## Twee onderdelen

| Onderdeel | Wat | Waar |
|---|---|---|
| **Marketing** | Landingspagina | GitHub Pages (live) |
| **App** | Registratie, dashboard, data | Lokaal (`app/`) → later VPS |

## Live marketing site

**https://jorispaarde.github.io/fizzybuzz/**

Deployt automatisch bij push naar `block3_joris` (alleen `index.html` + `css/`).

## Laravel app lokaal draaien

```bash
cd app
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install && npm run build
php artisan serve
```

Open **http://localhost:8000** — zie [`app/README.md`](app/README.md) voor details.

## Marketing preview (statisch)

```bash
npm start
```

Open http://localhost:3000
