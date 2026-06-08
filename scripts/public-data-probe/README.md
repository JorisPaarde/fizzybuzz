# Publieke databron-probe

Test welke horeca-prijsdata **zonder account** binnen te halen is.

## Gebruik

```bash
cd scripts/public-data-probe
pip install -r requirements.txt
python probe.py
```

Output: console-rapport + `results/latest.json`

## Methodes

| ID | Bron | Account | Prijzen | EAN | Status |
|---|---|---|---|---|---|
| `sligro_promotions_api` | Sligro `/api/product-overview/.../promotion/query` | Nee | Nee | Ja (GTIN) | ~1355 promo-producten |
| `sligro_folder_pdf` | Sligro `acties.html` → Publitas PDF | Nee | Ja (actie) | Nee | Art.nr + promo-prijzen |
| `hanos_occ_api` | `api.hanos.nl/occ/v2/hanos-nl` | Nee | Ja | Nee* | ~27k producten |
| `open_food_facts` | world.openfoodfacts.org | Nee | Nee | Ja | EAN-verrijking |
| `blocked_wholesalers` | Bidfood, Makro | — | — | — | 403 (WAF) |

\* HANOS detail-endpoint kan soms meer velden hebben; EAN zit niet standaard in search.

## Wat wél / niet zonder account

**Wél:**
- Sligro promo-catalogus (naam, EAN, verpakking) — geen klantprijzen
- Sligro folder-PDF's met actieprijzen en artikelnummers
- HANOS volledige catalogus-API met prijzen en actielabels
- Open Food Facts voor EAN → productnaam

**Niet (zonder account of geblokkeerd):**
- Sligro klantspecifieke inkoopprijzen (prijzen laden client-side na login)
- Bidfood / Makro (Cloudflare 403 op server-requests)
- HANOS EAN in bulk via search

## Volgende stap

Integreer succesvolle bronnen in Laravel sync-jobs (`app/Console/Commands/`).
