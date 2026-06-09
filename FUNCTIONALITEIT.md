# PriceSignal — Functioneel overzicht

> **Dit document is de enige bron van waarheid voor productfunctionaliteit.**
> Elke agent die aan dit project werkt, leest dit bestand eerst en werkt alleen aan functionaliteit die hierin staat beschreven — of werkt dit document bij vóór implementatie van nieuwe features.

**Laatste update:** juni 2026  
**Versie document:** 1.12  
**Live site:** https://jorispaarde.github.io/fizzybuzz/  
**Taal product:** Nederlands (NL)

---

## Inhoudsopgave

| § | Onderwerp |
|---|---|
| [1](#1-productvisie) | Productvisie |
| [2](#2-doelgroep) | Doelgroep |
| [3](#3-gebruikersrollen) | Gebruikersrollen |
| [4](#4-functionele-modules) | Functionele modules |
| [5](#5-gebruikersflows) | Gebruikersflows |
| [6](#6-datamodel-conceptueel) | Datamodel (conceptueel) |
| [7](#7-niet-functionele-eisen) | Niet-functionele eisen |
| [8](#8-technische-context-huidige-staat) | Technische context |
| [9](#9-wat-expliciet-niet-in-scope-is-voorlopig) | Buiten scope |
| [10](#10-ontwerprichtlijnen) | Ontwerprichtlijnen |
| [11](#11-roadmap-voorgestelde-volgorde) | Roadmap |
| [12](#12-werkwijze-voor-agents) | Werkwijze agents |

### Module-index (§4)

| Module | Onderwerp | Status |
|---|---|---|
| [4.1](#41-landingspagina-marketing) | Landingspagina | ✅ |
| [4.2](#42-registratie--lidmaatschap) | Registratie & lidmaatschap | 🟡 |
| [4.3](#43-prijsupload) | Prijsupload | ✅ |
| [4.4](#44-prijsvergelijking--inzicht) | Prijsvergelijking & inzicht | ✅ |
| [4.5](#45-groothandelsbeheer) | Groothandelsbeheer | 🟡 |
| [4.6](#46-productcatalogus--ean-database) | Productcatalogus & EAN | 🔲 |
| [4.7](#47-onderhandelingsondersteuning) | Onderhandelingsrapporten | 🔲 |
| [4.8](#48-anonimiteit--privacy) | Anonimiteit & privacy | 🔲 |
| [4.9](#49-notificaties) | Notificaties | 🔲 |
| [4.10](#410-beheer--moderatie) | Beheer & moderatie | 🔲 |
| [4.11](#411-externe-prijsdata--databronnen) | Externe prijsdata | 🔲 |
| [4.12](#412-mijn-producten--productdetail) | Mijn producten & productdetail | 🟡 |

**Gerelateerde docs:** [`docs/HOMEPAGE.md`](docs/HOMEPAGE.md) (homepage-structuur) · [`docs/DATAMODEL.md`](docs/DATAMODEL.md) (database)

---

## 1. Productvisie

**PriceSignal** (pricesignal.nl) is een platform waar horecabedrijven (restaurants, cafés, hotels en vergelijkbare ondernemingen) de inkoopprijzen die zij betalen bij groothandels met elkaar vergelijken.

### Kernprobleem

Groothandels hanteren **ondoorzichtige, klantspecifieke prijzen**. Twee vergelijkbare horecabedrijven kunnen voor hetzelfde product tot wel 30% prijsverschil betalen, zonder dat ze dit weten. Zonder benchmark is onderhandelen met leveranciers vrijwel onmogelijk.

### Oplossing

Leden **uploaden de prijzen die zij betalen**. Die gegevens worden **geaggreerd en anoniem gedeeld** als collectieve marktkennis. Daarmee krijgt elk lid:

- inzicht in wat de markt betaalt (per product, per groothandel)
- een sterke onderhandelingspositie met harde cijfers
- structureel lagere inkoopkosten

### Missie

Een einde maken aan ondoorzichtige prijzen in de horeca-inkoop, zodat **alle leden netto lagere prijzen** betalen.

### Waar zit de echte waarde?

De actuele prijs is nuttig, maar **de echte waarde zit in prijsverloop en verschillen tussen leveranciers** — informatie die de meeste horecaondernemers nu niet centraal kunnen zien. Prijsplein combineert daarom:

1. **Ledenprijzen** (crowdsourced, anoniem geaggregeerd)
2. **Externe prijsdata** (groothandels, folders, calculatiebestanden — zie §4.11)
3. **EAN-koppeling** over leveranciers heen (zelfde product, andere prijs)

### Kernprincipes

| Principe | Betekenis |
|---|---|
| **Geven en nemen** | Wie prijzen deelt, krijgt toegang tot gedeelde kennis |
| **Anonimiteit** | Individuele bedrijven zijn nooit herleidbaar in gedeelde data |
| **Collectieve kracht** | Meer leden = completer beeld = sterkere positie voor iedereen |
| **Transparantie** | Geen verborgen tarieven; marktprijzen worden zichtbaar |
| **Praktisch nut** | Alles wat gebouwd wordt, moet direct helpen bij onderhandelen |
| **Historisch inzicht** | Prijstrends en leveranciersverschillen zijn minstens zo waardevol als de actuele prijs |

---

## 2. Doelgroep

### Primaire gebruikers

- **Horecaondernemers** — eigenaren en managers van restaurants, cafés, hotels, cateringbedrijven
- **Inkoopverantwoordelijken** — medewerkers die leveranciers en prijzen beheren

### Bedrijfsgrootte

Van klein buurtcafé tot groot hotel. Het platform moet voor alle schalen bruikbaar zijn.

### Geografische focus

Nederland (primair). Prijzen in euro's. Nederlandse taal in de interface.

---

## 3. Gebruikersrollen

| Rol | Beschrijving | Status |
|---|---|---|
| **Bezoeker** | Niet-ingelogde gebruiker; ziet landingspagina | ✅ Geïmplementeerd |
| **Lid** | Geregistreerd horecabedrijf; kan prijzen uploaden en vergelijken | 🟡 Upload + vergelijking live; dashboard basis |
| **Beheerder** | Platformbeheer; moderatie, datakwaliteit, gebruikersbeheer | 🔲 Nog te bouwen |

---

## 4. Functionele modules

Onderstaande modules beschrijven de volledige beoogde functionaliteit. Per module staat de **status** vermeld.

**Legenda:** ✅ Geïmplementeerd · 🟡 Deels · 🔲 Nog te bouwen

---

### 4.1 Landingspagina (marketing)

**Doel:** In 30 seconden duidelijk maken wat PriceSignal doet, wat het oplevert, en bezoekers overtuigen om lid te worden.

**Status:** ✅ Geïmplementeerd (`index.html`)

**Volledige structuur, Sutherland-uitgangspunten en wijzigingschecklist:** [`docs/HOMEPAGE.md`](docs/HOMEPAGE.md)

#### Samenvatting structuur

| # | Sectie | Sutherland |
|---|---|---|
| 1 | Hero + pitch-strip + voorbeeldkaart | Reframe, informatie-asymmetrie |
| 2 | Wat je wint (3 opbrengsten) | Tangibel voordeel |
| 3 | Hoe het werkt (3 stappen) | Lage drempel |
| 4 | Waarom delen + bezwaren | Reciprociteit, reframing |
| 5 | Kosten van niets doen | Loss aversion |
| 6 | Vertrouwen + stats | Social proof |
| 7 | Aanmelden (CTA) | Commitment |

**Rondleiding:** aparte pagina (`rondleiding.html`), secundaire link — geen homepage-sectie.

#### Acceptatiecriteria

- [x] Responsive (mobiel + desktop)
- [x] Nederlandse teksten
- [x] Duidelijke uitleg van productdoel (wat het doet + wat het oplevert bovenaan)
- [x] Vaste sectievolgorde gedocumenteerd in `docs/HOMEPAGE.md`
- [x] Rondleiding secundair (niet hoofdverhaal)
- [ ] Werkend aanmeldformulier (koppeling met backend)

---

### 4.2 Registratie & lidmaatschap

**Doel:** Horecabedrijven kunnen zich aanmelden als lid.

**Status:** 🟡 Deels geïmplementeerd (`app/` — Laravel + Breeze)

#### Functionaliteit

- Aanmelden met e-mailadres (en later: bedrijfsgegevens)
- Verificatie van e-mailadres
- Bedrijfsprofiel aanmaken:
  - Bedrijfsnaam
  - Type horeca (restaurant, café, hotel, catering, overig)
  - Locatie (regio/stad — voor aggregatie, niet publiek per bedrijf)
  - **Jaaromzet** (omzetklasse — voor segmentatie bij vergelijkingen; zie §4.12)
  - Aantal medewerkers (optioneel)
- Akkoord met voorwaarden, met nadruk op anonimiteitsregels
- Inloggen / uitloggen
- Wachtwoord vergeten

#### Acceptatiecriteria

- [ ] Alleen geverifieerde horecabedrijven krijgen toegang (e-mailverificatie nog niet actief)
- [x] Eén account per bedrijf (uniek e-mailadres)
- [x] Duidelijke uitleg bij registratie over wat er gedeeld wordt en wat niet
- [x] Bedrijfsprofiel bij registratie (naam, type, regio, inkoopomvang) — *tijdelijk: maandelijkse inkoopomvang; wordt omzetklasse, zie §4.12*
- [x] Bedrijfsprofiel bewerkbaar in profiel (inkoopomvang, regio, type)
- [ ] Omzetklasse bij registratie en profiel (vervangt `purchase_size`; zie §4.12)
- [x] Basis-dashboard na inloggen
- [ ] Inloggen / uitloggen op productie-VPS

---

### 4.3 Prijsupload

**Doel:** Leden kunnen de prijzen die zij betalen zo makkelijk mogelijk invoeren of uploaden.

**Status:** ✅ Geïmplementeerd (`app/` — Fase 2 + 2b webhook)

#### Ontwerpprincipe

**Zo weinig mogelijk typewerk.** Foto, PDF of e-mail → automatisch uitlezen → lid controleert en corrigeert → pas dan opslaan.

#### Invoermethoden

| Methode | Beschrijving | Status |
|---|---|---|
| **Foto** | Foto van factuur of prijslijst (mobiel) | ✅ OpenAI Vision |
| **PDF** | PDF van factuur of prijslijst | ✅ OpenAI + tekstextractie |
| **E-mail** | Mail met PDF/bijlage naar upload-adres van het platform | ✅ Webhook (Mailgun) |
| **E-mail doorsturen** | Factuur of prijslijst **doorsturen** (forward) naar platform-adres | 🔲 Nog te bouwen |
| **Handmatig** | Product, groothandel, prijs, eenheid, datum invoeren | ✅ |

#### Extractie-flow (foto / PDF / e-mail)

```
Upload of e-mail ontvangen
    → OpenAI API: regels extraheren (product, prijs, eenheid)
    → Review-scherm: lid ziet alle regels en kan fouten corrigeren
    → Bevestigen → PriceSubmission records (status: pending)
    → Na goedkeuring → aggregatie (AnonymizationService)
```

**Belangrijk:** Geëxtraheerde data wordt **nooit direct opgeslagen**. Altijd eerst het review-scherm.

#### Techniek extractie

- **OpenAI API** (`gpt-4o-mini`) voor foto's en PDF's
- Foto: Vision API (base64 image)
- PDF: tekstextractie (`pdfparser`); bij gescande PDF's → lid krijgt tip om foto te uploaden
- API-key via `OPENAI_API_KEY` in `.env` (nooit in git)
- E-mail: inbound webhook (`POST /webhooks/inbound-email`) via Mailgun → zelfde extractie-pipeline

#### E-mail doorsturen (forward) — gepland

Leden ontvangen facturen en prijslijsten vaak al per e-mail van hun groothandel. In plaats van downloaden en opnieuw uploaden, moeten ze de ontvangen mail kunnen **doorsturen** naar een vast PriceSignal-adres (bijv. `upload@pricesignal.nl`).

**Gewenste flow:**

```
Lid ontvangt factuur-mail van groothandel
    → Doorsturen (forward) naar upload@pricesignal.nl
    → Platform herkent lid via afzender-e-mailadres
    → Bijlagen (PDF) en/of mailtekst → extractie-pipeline
    → Review-scherm in de app → bevestigen → opslaan
```

**Vereisten (nog te implementeren):**

- Duidelijk platform-adres zichtbaar in app en onboarding
- Herkenning van geregistreerd lid op basis van doorstuur-e-mailadres
- Ondersteuning voor doorgestuurde mails (incl. `Fwd:` / `Doorst:` en ingesloten bijlagen)
- Bevestiging per e-mail dat import ontvangen is (of foutmelding)
- Zelfde review-stap als bij foto/PDF — nooit direct opslaan

> **Technische basis:** inbound webhook (Mailgun) bestaat al; de volledige forward-ervaring voor eindgebruikers is nog niet af.

#### Gegevens per prijsregel

| Veld | Verplicht | Beschrijving |
|---|---|---|
| Productnaam | Ja | Bijv. "Tomaten" |
| Merk / specificatie | Nee | Bijv. "Cherry, NL" |
| Groothandel | Ja | Naam of selectie uit lijst |
| Prijs | Ja | Bedrag in euro's |
| Eenheid | Ja | Bijv. per kg, per liter, per doos, per stuk |
| Hoeveelheid per eenheid | Nee | Bijv. "5 kg", "6 x 1 L" |
| Datum | Ja | Datum waarop prijs geldt / factuurdatum — **altijd verplicht**; geen prijs zonder peildatum |
| Opmerking | Nee | Vrij tekstveld |

**Datumregel (platformbreed):** Elke prijs die wordt opgeslagen, getoond of vergeleken heeft een **peildatum** (`effective_date`). In de UI staat bij markt- en eigen prijzen altijd de datum (of periode) zichtbaar — geen “naakte” bedragen zonder context.

#### Acceptatiecriteria

- [x] Vier invoerkanalen: handmatig, foto, PDF, e-mail (webhook)
- [ ] Facturen doorsturen per e-mail (forward naar platform-adres)
- [x] Geëxtraheerde regels zijn bewerkbaar vóór opslaan (review-scherm)
- [x] Lid kan meerdere groothandels koppelen (`/my-wholesalers`)
- [x] Lid kan opgeslagen prijzen bewerken en verwijderen
- [x] Uploadgeschiedenis is zichtbaar voor het eigen bedrijf
- [x] Data pas na review opgeslagen; status `approved` na bevestiging door lid
- [x] Nederlandse foutmeldingen bij onvolledige invoer
- [x] OpenAI-fouten worden netjes getoond (geen crash)
- [x] Goedgekeurde upload voegt producten automatisch toe aan **Mijn producten** (§4.12)

---

### 4.4 Prijsvergelijking & inzicht

**Doel:** Leden kunnen zien wat de markt betaalt en hun eigen prijzen daarmee vergelijken.

**Status:** ✅ Fase 3a + 3b (`/compare`, `/dashboard`) · ✅ Publieke preview

#### Functionaliteit

- **Publieke preview** (`/compare` zonder login):
  - Zoeken op productnaam en groothandel/segment verkennen
  - Marktdata als teaser (aantal leden, groothandelnamen)
  - Prijzen en ranges verborgen — CTA om lid te worden
- **Zoeken** op productnaam of EAN (leden)
- **Vergelijken** per product:
  - Laagste, hoogste en gemiddelde ledenprijs (per inkoopomvang-segment)
  - Prijs per groothandel (geaggregeerd, anoniem)
  - Visuele marktrange (min–max) met positie van eigen prijs
  - **Externe referentieprijzen** (§4.11) waar beschikbaar — apart gelabeld
  - **Goedkoopste leverancier** bij EAN-match over groothandels
  - Aantal meetpunten (hoeveel leden; zichtbaar vanaf 1)
  - Peildatum bij elke prijs in vergelijking en historie
- **Eigen positie**: waar het eigen bedrijf staat t.o.v. de marktrange (onder / binnen / boven)
- **Omzetklasse**: vergelijkingen binnen dezelfde omzettranche (zie §4.12) — *huidige implementatie gebruikt nog maandelijkse inkoopomvang (`small` / `medium` / `large`)*
- **Filters**:
  - Groothandel
  - Productcategorie
  - Regio (geaggregeerd)
  - Periode (laatste 30/90/365 dagen)

#### Weergave

- Tabel- en kaartweergave
- Visuele indicatie: groen (onder range), rood (boven range), grijs (binnen range)
- Trend over tijd (ledendata + externe `price_history` — §4.11)

#### Acceptatiecriteria

- [x] Zoeken op productnaam (`/compare`)
- [x] Eigen prijs vs. marktrange (min–max) per groothandel
- [x] Visuele indicatie positie in marktrange (groen/rood)
- [x] Segmentatie op inkoopomvang bij aggregatie en vergelijking
- [ ] Marktdata tonen vanaf **1 meetpunt** — huidige code gebruikt nog drempel van 3 (§4.8)
- [ ] Elke getoonde prijs vermeldt peildatum
- [x] Eigen prijs uitgesloten bij marktpositie (geen zelfvergelijking)
- [x] Alleen geaggregeerde data zichtbaar; geen individuele bedrijven
- [x] Filters op vergelijking (groothandel, periode 30/90/365 dagen)
- [x] Dashboard-samenvatting: producten boven marktrange met directe links
- [x] Publieke preview zonder login (`/compare`) met gebluurde prijsdata en aanmeld-CTA
- [ ] Trend over tijd — Fase 3c / productdetail (§4.12)
- [ ] Export van vergelijkingsrapport (PDF) — Fase 4
- [ ] Persoonlijke productlijst als primaire vergelijkingsbron — §4.12

---

### 4.5 Groothandelsbeheer

**Doel:** Overzicht van groothandels waar leden inkopen en waar het platform externe data vandaan haalt.

**Status:** 🟡 Deels — ledenbeheer live (`/my-wholesalers`)

#### Functionaliteit

- Standaardlijst met bekende groothandels (Sligro, Bidfood, Hanos, etc.) — via seeder
- Leden kunnen groothandel toevoegen als deze niet in de lijst staat
- Leden kunnen groothandels koppelen/ontkoppelen aan hun profiel
- Gekoppelde groothandels staan bovenaan bij prijsinvoer
- Per groothandel: gemiddelde ledenprijs per productcategorie — 🔲 Fase 3b
- Koppeling met eigen prijsupload — ✅

#### Acceptatiecriteria

- [x] Standaardlijst groothandels beschikbaar
- [x] Lid kan groothandel koppelen en nieuwe toevoegen
- [x] Gekoppelde groothandels in upload-flow
- [ ] Groothandels zijn genormaliseerd (geen duplicaten door spelfouten)
- [ ] Beheerder kan groothandels samenvoegen en modereren — Fase 7

---

### 4.6 Productcatalogus & EAN-database

**Doel:** Gestandaardiseerde productnamen en cross-leverancier vergelijking via EAN.

**Status:** 🔲 Nog te bouwen

#### Functionaliteit

- Centrale productdatabase met categorieën (groenten, vlees, zuivel, dranken, etc.)
- **EAN/GTIN** als primaire koppelsleutel waar beschikbaar
- Fuzzy matching bij upload: "tomaat cherry 5kg" → "Tomaten, cherry"
- Suggesties bij handmatige invoer
- Nieuwe producten kunnen worden voorgesteld door leden
- **Cross-leverancier matching**: hetzelfde product bij Sligro, HANOS en Bidfood via EAN koppelen

#### EAN-vergelijkingstabel (concept)

| EAN | Product | Leverancier | Verpakking | Prijs | Datum |
|---|---|---|---|---|---|
| 871… | Coca Cola 24×33cl | Sligro | tray | € | 2026-06-08 |
| 871… | Coca Cola 24×33cl | HANOS | tray | € | 2026-06-08 |
| 871… | Coca Cola 24×33cl | Bidfood | tray | € | 2026-06-08 |

#### Mogelijkheden met EAN-koppeling

- Goedkoopste leverancier tonen per product
- Prijsverloop over tijd (historische prijzen)
- Automatische offertevergelijking
- Foodcost-berekening (koppeling met recepten/calculaties — zie §4.11 niveau 4)

#### Acceptatiecriteria

- [ ] Producten zijn vergelijkbaar over leden heen
- [ ] Eenheden worden genormaliseerd (alles omgerekend naar basiseenheid waar mogelijk)
- [ ] EAN wordt opgeslagen en gebruikt voor matching waar beschikbaar
- [ ] Zelfde EAN bij meerdere groothandels → één vergelijkingsweergave
- [ ] Beheerder kan producten samenvoegen

---

### 4.7 Onderhandelingsondersteuning

**Doel:** Leden helpen om met bewijs naar leveranciers te gaan.

**Status:** 🔲 Nog te bouwen

#### Functionaliteit

- **Benchmarkrapport** genereren per product of productgroep
- Overzicht: "Jij betaalt X, marktgemiddelde is Y, verschil Z%"
- **Gespreksvoorbereiding**: samenvatting met relevante marktdata
- **Doelprijs calculator**: wat zou een eerlijke prijs zijn op basis van marktdata

#### Acceptatiecriteria

- [ ] Rapport bevat geen herleidbare gegevens van andere individuele bedrijven
- [ ] Rapport is downloadbaar en deelbaar (PDF)
- [ ] Data in rapport is actueel (max. 90 dagen oud, tenzij anders vermeld)
- [ ] Rapport kan worden gegenereerd vanuit **Mijn producten** (§4.12) — gefilterde set of volledige lijst

---

### 4.12 Mijn producten & productdetail

**Doel:** Elk lid heeft een **persoonlijke productlijst** — de set artikelen waarvoor PriceSignal prijzen checkt en inzicht geeft. De lijst is het dagelijkse startpunt voor vergelijking, filters en onderhandeling.

**Status:** 🟡 Deels — Fase 3d-1 + 3d-2 live (`/my-products` lijst); filter, productdetail en omzetklasse volgen in 3d-3+

#### Navigatie

- Hoofdmenu-item: **Mijn producten** (`/my-products`) — ✅
- Bestaand **Vergelijken** (`/compare`) blijft voor vrije zoekopdrachten en gasten-preview
- Klik op een product in Mijn producten → **productdetailpagina** (`/my-products/{product}`)

#### 4.12.1 Persoonlijke productlijst

**Kernregel:** Alleen producten op deze lijst worden actief gemonitord voor het lid (dashboard-alerts, “elders goedkoper”-filter, onderhandelingsrapporten).

| Actie | Gedrag |
|---|---|
| **Automatisch toevoegen** | Bij goedkeuring van een upload (`price_submissions` met status `approved`): elk uniek `product_id` uit die upload komt op de lijst, tenzij het er al staat |
| **Handmatig toevoegen** | Zoekveld op productnaam (en later EAN); resultaat toevoegen met één klik |
| **Verwijderen** | Prullenbak-icoon per regel — verwijdert alleen de koppeling lid↔product, **niet** de onderliggende prijsdata of uploads |
| **Her-toevoegen** | Verwijderd product kan later opnieuw worden toegevoegd (handmatig of via nieuwe upload) |

**Lijstweergave (per regel):**

| Kolom | Inhoud |
|---|---|
| Product | Naam (+ categorie indien bekend) |
| Jouw prijs | Laatste goedgekeurde prijs bij jouw groothandel |
| Markt (jouw omzetklasse) | Laagste marktprijs of range + peildatum — zodra er marktdata is (≥1 meetpunt) |
| Verschil | Indicatie: elders goedkoper / vergelijkbaar / boven markt |
| Acties | Naar detail · verwijderen (prullenbak) |

**Sortering (standaard):** grootste potentiële besparing eerst (indien berekenbaar), anders alfabetisch.

#### 4.12.2 Filter: elders goedkoper

Toggle of filterchip: **“Elders goedkoper”**.

Toont alleen producten waar:

1. Het lid een eigen goedgekeurde prijs heeft, én
2. In de **eigen omzetklasse** (§4.12.4) een lagere anonieme marktprijs bestaat (bij welke groothandel dan ook)

**Geen minimumverschil** — elk bedrag dat lager is dan de eigen prijs kwalificeert; geen percentage-drempel.

Producten zonder enige marktdata in de omzetklasse vallen buiten dit filter.

#### 4.12.3 Productdetailpagina

**Route:** `/my-products/{product}` (leden) · optioneel publieke verkorte versie later

**Secties:**

| Sectie | Inhoud |
|---|---|
| **Header** | Productnaam, categorie, EAN (indien bekend), link terug naar Mijn producten |
| **Jouw situatie** | Jouw laatste prijs, groothandel, **peildatum**, positie t.o.v. markt in **jouw omzetklasse** |
| **Prijsgeschiedenis** | Grafiek op **datum**: eigen prijsverloop + anonieme marktlijn (zelfde omzetklasse). Periode: 30 / 90 / 365 dagen; elke punt = prijs op peildatum |
| **Leveranciers** | Tabel: groothandels die dit product leveren, met prijs **én peildatum** per regel (of via EAN hetzelfde artikel) |
| **Prijzen per omzetklasse** | Tabel met kolommen per omzettranche; **standaard geselecteerd: de klasse van het ingelogde lid**. Andere klassen uitklapbaar of via tabs |
| **Vergelijkbare producten** | Zelfde categorie of EAN-match — 🔲 afhankelijk van §4.6 |

**Interactie:**

- Wisselen omzetklasse op detailpagina verandert alle marktprijzen in de tabel (eigen prijs blijft van het lid)
- Geen ruwe prijzen van andere individuele bedrijven — alleen aggregaten (§4.8)
- Als lid geen eigen prijs heeft: detail toont alleen marktdata + CTA om prijs te uploaden of product uit lijst te halen

#### 4.12.4 Omzetklasse (segmentatie)

Vergelijkingen en aggregaties lopen binnen de **zelfde omzetklasse** — vergelijkbaar onderhandelingsvermogen en schaal.

| Sleutel | Label (NL) | Jaaromzet (indicatief) |
|---|---|---|
| `t100k` | Tot €100k | &lt; €100.000 |
| `t500k` | €100k – €500k | €100.000 – €500.000 |
| `t1m` | €500k – €1 mln | €500.000 – €1.000.000 |
| `t2m` | €1 – €2 mln | €1.000.000 – €2.000.000 |
| `t5m` | €2 – €5 mln | €2.000.000 – €5.000.000 |
| `t10m` | €5 – €10 mln | €5.000.000 – €10.000.000 |
| `t10m_plus` | €10 mln+ | ≥ €10.000.000 |

**Invulling:**

- Verplicht veld bij registratie en bewerkbaar in profiel
- Vervangt de huidige `purchase_size` (maandelijkse inkoopomvang: klein/middel/groot) in nieuwe implementatie
- Migratie bestaande leden: mapping of herkies bij eerste login na update (te bepalen bij implementatie)
- `aggregated_prices` en live-vergelijking segmenteren op `revenue_tranche` i.p.v. `purchase_size`

#### 4.12.5 Relatie met andere modules

| Module | Koppeling |
|---|---|
| §4.3 Prijsupload | Upload → goedgekeurde producten → automatisch op Mijn producten |
| §4.4 Vergelijken | Vrije zoekopdracht; resultaat kan aan Mijn producten worden toegevoegd |
| §4.6 EAN | Zelfde EAN over groothandels → één productdetail met leveranciersmatrix |
| §4.7 Rapport | PDF gebruikt producten uit Mijn producten (eventueel gefilterd) |
| §4.9 Notificaties | Alerts alleen voor producten op Mijn producten |
| Dashboard | “Boven marktrange”-inzicht baseert op Mijn producten i.p.v. alle uploads |

#### Acceptatiecriteria

**Mijn producten**

- [x] Tab/nav-item **Mijn producten** zichtbaar voor ingelogde leden
- [x] Lijst toont alle gekoppelde producten met eigen prijs en marktindicatie
- [x] Producten uit goedgekeurde uploads worden automatisch toegevoegd (geen duplicaten)
- [x] Zoeken en handmatig toevoegen van producten uit catalogus
- [x] Verwijderen via prullenbak-icoon (alleen lijstkoppeling, data blijft bewaard)
- [ ] Filter **Elders goedkoper** werkt binnen eigen omzetklasse
- [x] Lege staat met uitleg + link naar prijsupload

**Productdetail**

- [ ] Klik op product opent detailpagina met prijsgeschiedenis-grafiek
- [ ] Leverancierstabel met geaggregeerde prijzen per groothandel
- [ ] Prijzen per omzetklasse zichtbaar; standaard de klasse van het lid
- [ ] Wisselen van omzetklasse werkt zonder pagina-reload (filter/tab)
- [ ] Marktdata zichtbaar vanaf 1 meetpunt; elke prijs met peildatum (§4.8)

**Omzetklasse**

- [ ] Registratie en profiel vragen omzetklasse (7 tranches)
- [ ] Aggregatie en vergelijking gebruiken omzetklasse
- [ ] Migratiepad van `purchase_size` gedocumenteerd en uitgevoerd

**Technisch**

- [x] Pivot-tabel `user_products` (`user_id`, `product_id`, `added_via` enum: `upload` / `manual`, timestamps) — `UserProductService`, `MyProductController`
- [ ] `users.revenue_tranche` vervangt `users.purchase_size`
- [ ] `aggregated_prices.revenue_tranche` vervangt `purchase_size`-kolom
- Zie ook `docs/DATAMODEL.md`

---

### 4.8 Anonimiteit & privacy

**Doel:** Garanderen dat gedeelde data nooit tot individuele bedrijven herleidbaar is.

**Status:** 🔲 Nog te bouwen (regels vastgelegd, implementatie volgt)

#### Regels

1. **Tonen vanaf 1 meetpunt**: zodra er (anonieme) marktdata is voor een product/groothandel/omzetklasse, wordt die getoond — **geen minimum van 3 bijdragen**. Liever weinig data tonen dan niets; UI vermeldt het aantal meetpunten (`1 lid`, `2 leden`, …).
2. **Geen ranglijst van bedrijven**: nooit tonen welk bedrijf de laagste/hoogste prijs heeft
3. **Geen herleidbare ruwe data**: leden zien geen prijs gekoppeld aan een bedrijfsnaam; bij 1 meetpunt is de aggregatie dat ene anonieme punt
4. **Eigen data**: een lid ziet altijd zijn eigen ingevoerde prijzen volledig, inclusief peildatum
5. **Peildatum verplicht**: elke prijs in opslag en weergave heeft `effective_date` (§4.3)
6. **Regio-aggregatie**: locatiegegevens alleen op regionaal niveau, nooit per adres
7. **AVG-compliance**: verwerkersovereenkomst, recht op inzage en verwijdering

> **Implementatienoot:** `AnonymizationService::MIN_DATAPOINTS = 3` in de huidige code wijkt af van dit beleid en wordt bij Fase 3d aangepast naar **1**.

#### Acceptatiecriteria

- [ ] Marktdata wordt getoond bij ≥1 anoniem meetpunt (niet pas bij 3)
- [ ] Getoonde prijzen vermelden altijd peildatum
- [ ] Privacyregels zijn technisch afgedwongen, niet alleen in beleid
- [ ] Externe privacy-audit mogelijk
- [ ] Dataverwijdering bij opzegging lidmaatschap

---

### 4.9 Notificaties

**Doel:** Leden op de hoogte houden van relevante marktveranderingen.

**Status:** 🔲 Nog te bouwen

#### Triggers

- Eigen prijs wijkt significant af van marktgemiddelde (> 15%)
- Nieuw product beschikbaar in vergelijking (waar lid zelf ook data voor heeft)
- Nieuwe groothandel toegevoegd aan platform
- Periodieke samenvatting (maandelijks overzicht)

#### Kanalen

- E-mail (primair)
- In-app notificaties (later)

---

### 4.10 Beheer & moderatie

**Doel:** Platformbeheerders houden datakwaliteit en gebruikersbeheer bij.

**Status:** 🔲 Nog te bouwen

#### Functionaliteit

- Gebruikersbeheer (activeren, deactiveren, verwijderen)
- Moderatie van geüploade prijzen (flaggen, goedkeuren, afkeuren)
- Product- en groothandelsamenvoeging
- Dashboard met platformstatistieken (aantal leden, uploads, producten)
- Auditlog van beheerdersacties
- Overzicht externe datasyncs (status, fouten, laatste run)

---

### 4.11 Externe prijsdata & databronnen

**Doel:** Naast ledenuploads systematisch prijsdata verzamelen uit externe bronnen, zodat het platform ook zonder grote ledenbasis al waardevol is.

**Status:** 🔲 Nog te bouwen

#### Strategie: zes niveaus van data-acquisitie

Onderstaande niveaus zijn **onderzoeks- en implementatiepaden**, niet alles tegelijk. Elk niveau bouwt voort op het vorige.

| Niveau | Bron | Kwaliteit | Beschrijving |
|---|---|---|---|
| **1** | Officiële API's / partnerkoppelingen | Hoogst | Sligro (Apicbase, AFAS, OCI), HANOS-integraties, PS in Foodservice productdatabase |
| **2** | Inloggen + gestructureerde extractie | Hoog | Zakelijke accounts; productpagina's crawlen; JSON-endpoints onderscheppen (Playwright) |
| **3** | Digitale folders & aanbiedingen | Middel | PDF-folders downloaden → OCR/extractie → historische promo-database |
| **4** | Recept- en calculatiebestanden | Hoog (leden) | Sligro calculatiedocumenten met inkoop-, verkoopprijs en marge — upload + extractie |
| **5** | Meer groothandels | Variabel | Bidfood, Makro, VHC, FOOX, MELEDI, Lekkerland naast Sligro/HANOS |
| **6** | EAN-database | Fundament | Cross-leverancier matching; zie §4.6 |

#### Niveau 1 — Officiële API's (eerste onderzoekspad)

Veel groothandels hebben geen publieke API, maar wel partnerkoppelingen:

- **Sligro** — koppelingen met Apicbase, AFAS, OCI en andere inkoopplatforms; actuele prijzen worden uitgewisseld
- **HANOS** — vergelijkbare integraties; product- en prijsdata automatisch gesynchroniseerd
- **PS in Foodservice** — centrale productdatabase met foodservice-productinformatie via API-koppelingen

**Platformfunctionaliteit:**

- Onderzoeks- en integratiemodule per leverancier (status: onderzoek / pilot / live)
- OAuth of API-key opslag per zakelijke koppeling (versleuteld, alleen platformbeheerder)
- Geplande sync (dagelijks) naar `ExternalPrice` records
- Bronvermelding in vergelijkings-UI: "Sligro API" vs. "lid-upload" vs. "folder"

#### Niveau 2 — Inloggen + gestructureerde extractie

Bij Sligro en HANOS zijn prijzen zichtbaar na login. Technische aanpak:

1. Zakelijk account aanmaken (platform of testaccount)
2. Playwright: inloggen en productpagina's crawlen
3. Productnaam, EAN, verpakking, prijs, aanbieding opslaan
4. Vaak laden pagina's JSON in de achtergrond — endpoints onderscheppen i.p.v. HTML scrapen

**Technische componenten (Laravel):**

- `app/Console/Commands/` — geplande sync-jobs per leverancier
- `app/Services/Scrapers/` — per groothandel een scraper-adapter (Playwright via Node of Python sidecar)
- Response-interceptie: verborgen API-endpoints loggen en hergebruiken
- Rate limiting en foutafhandeling; geen agressieve parallelle requests

**Juridisch / ethisch:**

- Alleen data verzamelen waarvoor een geldig zakelijk account bestaat
- Gebruiksvoorwaarden groothandels respecteren; juridisch advies vóór productie-scraping
- Geen credentials van leden gebruiken zonder expliciete toestemming

#### Niveau 3 — Folders & aanbiedingen

Sligro en anderen publiceren digitale folders online.

**Functionaliteit:**

- PDF-folders downloaden (geplande job of handmatige upload door beheerder)
- OCR + extractie (OpenAI Vision of dedicated OCR)
- Producten, actieprijzen en geldigheidsperiode opslaan
- **Historische database**: seizoenspatronen, actiefrequentie, gemiddelde korting na ~1 jaar data

**Waarde:** promo-prijzen zijn vaak de enige "publieke" prijsindicatie; historisch verloop is onderhandelingsmateriaal.

#### Niveau 4 — Recept- en calculatiebestanden

Sligro levert klanten calculatiedocumenten met inkoopprijzen, hoeveelheden, verkoopprijzen en marges.

**Platformkoppeling:**

- Upload via bestaande §4.3-pipeline (PDF) met extractie-profiel "calculatie"
- Velden: ingrediënt, inkoopprijs, verkoopprijs, marge %, receptnaam
- Koppeling met §4.6 EAN waar mogelijk → foodcost per gerecht
- **Anonimiteit:** calculaties van leden vallen onder ledenregels (§4.8); platform-eigen testdata apart gelabeld

#### Niveau 5 — Uitbreiding groothandels

Standaardlijst (uitbreiden naast huidige seeder):

| Groothandel | Prioriteit | Niveau 1 API | Niveau 2 scrape | Niveau 3 folders |
|---|---|---|---|---|
| Sligro | P1 | Onderzoek | Ja | Ja |
| HANOS | P1 | Onderzoek | Ja | Deels |
| Bidfood | P2 | Onderzoek | Ja | Deels |
| Makro | P2 | — | Ja | Ja |
| VHC | P3 | — | Onderzoek | — |
| FOOX | P3 | — | Onderzoek | — |
| MELEDI | P3 | — | Onderzoek | — |
| Lekkerland | P3 | — | Onderzoek | — |

Elke extra leverancier verhoogt de waarde van de EAN-database (§4.6).

#### Niveau 6 — EAN-database (fundament)

Zie §4.6. Externe bronnen (niveau 1–5) voeden primair de EAN-database; ledenuploads valideren en verrijken die data.

#### MVP-pad externe data (technisch)

| Stap | Omschrijving | Doel |
|---|---|---|
| 1 | Sligro zakelijk account + Playwright proof-of-concept | Endpoints vinden |
| 2 | PostgreSQL + `external_prices` tabel + dagelijkse sync-job | 10.000+ producten |
| 3 | HANOS-scraper toevoegen | Cross-leverancier |
| 4 | EAN matching + historische prijzen (`price_history`) | Trends & vergelijking |
| 5 | Folder-pipeline (PDF → extractie) | Promo-historie |

#### Acceptatiecriteria

- [ ] Per databron: bron-type, leverancier, sync-datum en betrouwbaarheid zichtbaar in UI
- [ ] Externe prijzen gescheiden van ledenaggregaties (andere privacyregels)
- [ ] Historische prijzen bewaard (niet overschrijven bij update)
- [ ] EAN wordt waar mogelijk meegenomen bij elke externe import
- [ ] Sync-fouten loggen en tonen in beheerdersdashboard
- [ ] Juridische review vóór productie-scraping gedocumenteerd

---

## 5. Gebruikersflows

### Flow 1: Bezoeker wordt lid

```
Landingspagina → Aanmelden (e-mail) → Verificatie → Bedrijfsprofiel invullen
→ Akkoord voorwaarden → Dashboard (nog geen data) → Eerste prijsupload
```

### Flow 2: Lid uploadt prijzen

```
Dashboard → "Prijzen toevoegen" → Kies methode (handmatig / bestand)
→ Vul gegevens in → Bevestig → Data verschijnt in eigen overzicht
→ Na validatie: opgenomen in gedeelde statistieken
```

### Flow 3: Lid vergelijkt prijzen

```
Dashboard → Zoek product → Vergelijkingsoverzicht
→ Zie marktgemiddelde vs. eigen prijs → Genereer benchmarkrapport
→ Gebruik rapport in gesprek met groothandel
```

### Flow 4: Lid onderhandelt

```
Vergelijking → Significant verschil gedetecteerd → Benchmarkrapport downloaden
→ Gesprek met leverancier → Nieuwe prijs → Update eigen prijs in platform
```

### Flow 5: Platform verrijkt data (achtergrond)

```
Geplande sync (cron) → Scraper/API/folder-job per groothandel
→ Product + EAN + prijs + datum → external_prices + price_history
→ EAN matching met productcatalogus → Beschikbaar in vergelijking (§4.4)
```

### Flow 6: Lid vergelijkt leveranciers op EAN

```
Zoek product (naam of EAN) → Zelfde product bij Sligro / HANOS / Bidfood
→ Goedkoopste leverancier + prijsverloop grafiek → Export voor onderhandeling
```

---

## 6. Datamodel (conceptueel)

```
Bedrijf (lid)
  ├── id, naam, type, regio, e-mail, aangemaakt_op
  └── Prijsregels[]
        ├── product_id
        ├── groothandel_id
        ├── prijs, eenheid, datum
        └── bron (handmatig / prijslijst / factuur)

Groothandel
  ├── id, naam, categorieën[]
  └── (geaggregeerde statistieken, geen ruwe data)

Product
  ├── id, naam, ean (optioneel), categorie, standaard_eenheid
  └── (geaggregeerde statistieken)

ExternePrijs (platform-verzameld, niet van leden)
  ├── id, product_id, groothandel_id, ean
  ├── prijs, eenheid, verpakking, is_actie (boolean)
  ├── bron (api / scrape / folder / calculatie)
  ├── sync_job_id, opgehaald_op
  └── (los van ledenaggregatie; geen anonimiteitsdrempel)

PrijsHistorie
  ├── external_price_id of price_submission_id
  ├── prijs, geldig_vanaf, geldig_tot
  └── (append-only; nooit overschrijven)

DatasyncJob
  ├── groothandel_id, type (api / scrape / folder)
  ├── status, gestart_op, voltooid_op, aantal_records
  └── foutlog

Aggregatie (berekend, niet opgeslagen als ruwe data)
  ├── product_id + groothandel_id
  ├── gemiddelde, mediaan, min, max, aantal_datapunten
  └── periode
```

---

## 7. Niet-functionele eisen

| Eis | Richtlijn |
|---|---|
| **Taal** | Nederlands in alle gebruikersgerichte teksten |
| **Responsiveness** | Mobile-first; horecaondernemers gebruiken vaak mobiel |
| **Toegankelijkheid** | WCAG 2.1 AA als streefdoel |
| **Performance** | Pagina's laden in < 3 seconden op mobiel |
| **Beveiliging** | HTTPS, versleutelde opslag van bedrijfsgegevens, geen publieke API voor ruwe prijsdata |
| **Beschikbaarheid** | 99% uptime als streefdoel (na backend-lancering) |

---

## 8. Technische context (huidige staat)

| Onderdeel | Technologie | Status |
|---|---|---|
| Landingspagina | Statische HTML + CSS | ✅ Live op GitHub Pages |
| Applicatie | Laravel 13 + Breeze (Blade) | 🟡 Lokaal draaibaar |
| Database (dev) | SQLite | ✅ Werkend |
| Database (prod) | PostgreSQL op VPS | 🔲 Bij VPS-deploy |
| Authenticatie | Laravel Breeze | 🟡 Registratie + login lokaal |
| Anonimisering | `AnonymizationService` | 🟡 Aggregatie per segment; drempel 3 → wordt 1 (§4.8) |
| Prijsvergelijking | `PriceComparisonService` + `/compare` | ✅ Zoeken, marktrange, inkoopsegment |
| Mijn producten | `/my-products` + productdetail | 🟡 Lijst live; detail §4.12.3 nog te bouwen |
| Omzetklasse | `revenue_tranche` op users + aggregatie | 🔲 Vervangt `purchase_size` (§4.12) |
| Prijsupload | Handmatig + foto/PDF + e-mail + review | ✅ Lokaal |
| AI-extractie | OpenAI `gpt-4o-mini` | 🟡 Via `OPENAI_API_KEY` |
| Externe data-sync | Playwright + Laravel jobs | 🔲 Zie §4.11 |
| EAN-matching | PostgreSQL + productcatalogus | 🔲 Zie §4.6 |
| Betaling (later) | Stripe + Laravel Cashier | 🔲 Voorbereid in architectuur |
| Hosting marketing | GitHub Pages | ✅ Live |
| Hosting app (later) | Hetzner VPS | 🔲 Te deployen |

### Architectuurkeuze (levelsio-stijl)

- **Monoliet:** alles in één Laravel-project (`app/`)
- **Zelf te beheren:** één VPS, geen vendor lock-in (geen Clerk/Vercel/Supabase)
- **Twee omgevingen:**
  - GitHub Pages → alleen statische marketing (`index.html`, `css/`)
  - Laravel lokaal/VPS → registratie, dashboard, data

### Repository

- **Repo:** `JorisPaarde/fizzybuzz`
- **Branch:** `block3_joris` (hoofdbranch)
- **Marketing URL:** https://jorispaarde.github.io/fizzybuzz/
- **App lokaal:** http://localhost:8000 (zie `app/README.md`)

### Publieke databronnen (zonder account) — onderzoek juni 2026

Getest via `scripts/public-data-probe/probe.py`. Resultaten in `scripts/public-data-probe/results/latest.json`.

| Bron | Methode | Prijzen | EAN | Volume | Account nodig? |
|---|---|---|---|---|---|
| **HANOS** | `api.hanos.nl/occ/v2/hanos-nl/products/search` | ✅ Ja (incl. actie) | ❌ | ~27k producten | Nee |
| **Sligro folders** | `acties.html` → Publitas PDF | ✅ Actieprijzen | ❌ | 11 folders | Nee |
| **Sligro promo** | `/api/product-overview/sligro-nl/nl/promotion/query` | ❌ | ✅ GTIN | ~1355 promo's | Nee |
| **Open Food Facts** | `/api/v2/product/{ean}.json` | ❌ | ✅ | Miljoenen | Nee |
| **Bidfood / Makro** | Website | — | — | Geblokkeerd (403/WAF) | Waarschijnlijk wel |

**Conclusie:** start sync met HANOS OCC + Sligro folder-PDF's. Sligro promo-API levert EAN-catalogus; prijzen daar pas na login. Bidfood/Makro vereisen Playwright + account.

### Bestandsstructuur (huidig)

```
/
├── FUNCTIONALITEIT.md    ← dit document
├── README.md             ← repo-overzicht
├── docs/
│   ├── DATAMODEL.md      ← databaseschema & relaties
│   └── HOMEPAGE.md       ← landingspagina structuur & copy-uitgangspunten
├── index.html            ← landingspagina (GitHub Pages)
├── css/style.css         ← marketing-styling
├── scripts/public-data-probe/  ← publieke databron-tests (Python)
├── app/                  ← Laravel-applicatie
│   ├── app/Models/       ← User, Product, Wholesaler, PriceSubmission, AggregatedPrice, …
│   ├── app/Services/     ← AnonymizationService, PriceComparisonService, PriceExtractionService, Scrapers/ (later)
│   ├── app/Console/      ← geplande sync-commands (later)
│   ├── database/         ← migraties + SQLite
│   └── README.md         ← lokale setup-instructies
└── .github/workflows/    ← GitHub Pages deploy (alleen statische site)
```

---

## 9. Wat expliciet níet in scope is (voorlopig)

- Directe inkoop / bestellingen bij groothandels via het platform
- Betalingen of financiële transacties
- Beoordelingen of recensies van groothandels
- B2B-marktplaats of doorverkoop tussen horecabedrijven
- Internationale uitbreiding buiten Nederland
- AI-chatbot of automatische onderhandeling met leveranciers
- Scraping zonder geldig zakelijk account of in strijd met leveranciersvoorwaarden (zie §4.11 juridisch)
- Opslag of delen van inloggegevens van leden voor groothandel-scraping zonder expliciete toestemming

---

## 10. Ontwerprichtlijnen

### Visuele identiteit (huidige landingspagina)

| Element | Waarde |
|---|---|
| Merknaam | PriceSignal — pricesignal.nl |
| Achtergrond | Licht (#f7f9fc) |
| Primair | Corporate navy (#0b2545) |
| Accent | Trust blue (#2e6ba8) |
| Tekst | Donker (#1a2b3c) |
| Font | Source Sans 3 (sans-serif) |
| Toon | Corporate, betrouwbaar, professioneel — geen jargon |

### Tone of voice

- Spreek de horecaondernemer direct aan ("jij", "jouw")
- Kort en concreet; geen buzzwords
- Focus op praktisch voordeel: lagere prijzen, beter onderhandelen
- Benadruk samenwerking en eerlijkheid

---

## 11. Roadmap (voorgestelde volgorde)

| Fase | Omschrijving | Modules |
|---|---|---|
| **Fase 0** | Landingspagina | 4.1 ✅ |
| **Fase 1** | Registratie & basis-dashboard | 4.2 🟡 |
| **Fase 2** | Prijsupload (handmatig + foto/PDF + review) | 4.3 ✅ |
| **Fase 2b** | E-mail upload (inbound mail) | 4.3 (e-mail) ✅ |
| **Fase 2c** | E-mail doorsturen (forward facturen) | 4.3 (forward) 🔲 |
| **Fase 3a** | Vergelijking basis (zoeken + marktrange + inkoopsegment) | 4.4 ✅ |
| **Fase 3b** | Filters + dashboard-inzicht | 4.4 ✅ |
| **Fase 3c** | Productcatalogus & matching | 4.6 |
| **Fase 3d** | Mijn producten + productdetail + omzetklasse | 4.12 ← **volgende stap** |
| **Fase 4** | Onderhandelingsrapporten (PDF) | 4.7 |
| **Fase 5** | Bestandsupload (prijslijsten/facturen) | 4.3 (bestand) |
| **Fase 6** | Notificaties | 4.9 |
| **Fase 7** | Beheer & moderatie | 4.10 |
| **Fase 8** | EAN-database & productmatching | 4.6 (EAN) |
| **Fase 9a** | Externe data — Sligro scraper MVP | 4.11 niveau 2 |
| **Fase 9b** | Externe data — HANOS + historische prijzen | 4.11 niveau 2, 6 |
| **Fase 10** | Folder-pipeline (promo-historie) | 4.11 niveau 3 |
| **Fase 11** | Officiële API-koppelingen (Sligro/HANOS/PS) | 4.11 niveau 1 |
| **Fase 12** | Meer groothandels (Bidfood, Makro, …) | 4.11 niveau 5 |
| **Fase 13** | Calculatiebestanden & foodcost | 4.11 niveau 4 |

**Prioriteit externe data:** Fase 8 (EAN) kan parallel met Fase 3; Fase 9a is het technische MVP (Sligro + PostgreSQL + dagelijkse sync). Ledenupload (Fase 2) blijft de kern voor anonimisering; externe data maakt het platform ook waardevol vóór kritische massa leden.

> **Aanpassingen aan deze roadmap:** altijd eerst dit document bijwerken, daarna implementeren.

### Huidige MVP-status (juni 2026)

| Stap | Wat de gebruiker kan | Status |
|---|---|---|
| 1. Ontdekken | Landingspagina met uitleg | ✅ Live |
| 2. Aanmelden | Registratie met bedrijfsprofiel + inkoopomvang | ✅ Lokaal |
| 3. Prijzen delen | Foto, PDF, e-mail, handmatig → review → opslaan | ✅ Lokaal |
| 4. Vergelijken | Zoek product → marktrange (leden) of preview (gasten) | ✅ Lokaal |
| 5. Inzicht | Dashboard: welke producten zijn duurder dan markt? | ✅ Lokaal |
| 6. Mijn producten | Persoonlijke lijst, elders-goedkoper-filter, productdetail | 🟡 Lijst live (3d-1/2); filter + detail volgen |
| 7. Onderhandelen | PDF-rapport met harde cijfers | 🔲 Fase 4 |
| 8. Productie | App op VPS, echte gebruikers | 🔲 Deploy |

**Volgende stap MVP:** **Fase 3d-3 t/m 3d-6** — filter elders goedkoper, productdetail met prijsgeschiedenis, omzetklasse-segmentatie en marktdrempel 1 meetpunt (§4.12). Daarna Fase 4 (PDF-rapport).

Parallel optioneel: **VPS-deploy** (Fase 1 afronden) zodat early adopters de app kunnen testen.

---

## 12. Werkwijze voor agents

1. **Lees dit document** volledig vóór je begint
2. **Check de status** van de module die je gaat bouwen
3. **Werk dit document bij** als je functionaliteit toevoegt, wijzigt of afrondt (status + acceptatiecriteria)
4. **Houd scope klein** — bouw alleen wat in de actuele fase staat
5. **Wijzig geen andere modules** dan nodig voor de huidige taak
6. **Taal:** alle gebruikersgerichte teksten in het Nederlands
7. **Test op mobiel** — de primaire gebruiker is op telefoon
8. **Commit en deploy** via `block3_joris`; GitHub Pages deployt automatisch

---

*Dit document wordt bij elke significante productbeslissing bijgewerkt.*
