# PriceSignal

**pricesignal.nl** — platform waar horecabedrijven inkoopprijzen bij groothandels vergelijken en samen scherpere tarieven onderhandelen.

> **Productdocumentatie:** [`FUNCTIONALITEIT.md`](FUNCTIONALITEIT.md) — lees dit eerst.  
> **Datamodel:** [`docs/DATAMODEL.md`](docs/DATAMODEL.md)  
> **Installatie:** [`SETUP.md`](SETUP.md) — checklist, API-keys, server setup.

## Twee onderdelen

| Onderdeel | Wat | Waar |
|---|---|---|
| **Marketing** | Landingspagina | GitHub Pages (live) |
| **App** | Registratie, prijsupload, vergelijking | Lokaal (`app/`) → later VPS |

## Wat werkt (MVP)

| Feature | Route | Status |
|---|---|---|
| Registratie + bedrijfsprofiel (inkoopomvang) | `/register` | ✅ |
| Prijsupload (foto, PDF, e-mail, handmatig) | `/prices` | ✅ |
| Factuur doorsturen per e-mail (forward) | — | 🔲 Gepland |
| Groothandels koppelen | `/my-wholesalers` | ✅ |
| Prijsvergelijking met marktrange | `/compare` | ✅ (leden) |
| Publieke preview (prijzen verborgen) | `/compare` | ✅ (gasten) |
| Dashboard-inzicht (boven markt) | `/dashboard` | ✅ |
| Mijn producten (persoonlijke lijst) | `/my-products` | 🔲 Fase 3d |
| Productdetail + prijsgeschiedenis | `/my-products/{product}` | 🔲 Fase 3d |
| Omzetklasse-segmentatie | profiel | 🔲 Fase 3d |
| Onderhandelingsrapport (PDF) | — | 🔲 Fase 4 |

Zie [`FUNCTIONALITEIT.md`](FUNCTIONALITEIT.md) voor de volledige roadmap.

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
