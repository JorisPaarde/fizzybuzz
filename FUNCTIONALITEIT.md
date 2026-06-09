# PriceSignal — Functioneel overzicht

> **Dit document is de enige bron van waarheid voor productfunctionaliteit.**
> Elke agent die aan dit project werkt, leest dit bestand eerst en werkt alleen aan functionaliteit die hierin staat beschreven — of werkt dit document bij vóór implementatie van nieuwe features.

**Laatste update:** juni 2026  
**Versie document:** 1.7  
**Live site:** https://jorispaarde.github.io/fizzybuzz/  
**Taal product:** Nederlands (NL)

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

**Doel:** Uitleggen wat PriceSignal is en bezoekers overtuigen om lid te worden.

**Status:** ✅ Geïmplementeerd (`index.html`)

#### Secties

| Sectie | Inhoud |
|---|---|
| Header | Logo, navigatie (Hoe het werkt, Waarom, Voordelen), CTA "Word lid" |
| Hero | Waardepropositie + visuele prijsvergelijkingskaart (voorbeelddata) |
| Probleem | Drie pijnpunten: ondoorzichtige prijzen, geen vergelijkingspunt, stijgende kosten |
| Hoe het werkt | Drie stappen: uploaden → vergelijken → onderhandelen |
| Voordelen | Vier voordelen + quote + statistieken |
| Product | Wat PriceSignal doet: delen → anonimiseren → ontvangen |
| Waarom delen | Reciprociteit, reframing bezwaren, uitwisselingskaart |
| Probleem | Loss framing: kosten van geen inzicht |
| Hoe het werkt | Drie stappen incl. foto/PDF/e-mail |
| Voordelen | Anonimiteit, collectief, signaling |
| Samen | Netwerkeffect / social proof |
| Aanmelden | E-mailformulier (placeholder, nog niet functioneel) |
| Footer | Tagline en copyright |

#### Acceptatiecriteria

- [x] Responsive (mobiel + desktop)
- [x] Nederlandse teksten
- [x] Duidelijke uitleg van productdoel
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
  - Maandelijkse inkoopomvang (klein / middel / groot — voor segmentatie)
  - Aantal medewerkers (optioneel)
- Akkoord met voorwaarden, met nadruk op anonimiteitsregels
- Inloggen / uitloggen
- Wachtwoord vergeten

#### Acceptatiecriteria

- [ ] Alleen geverifieerde horecabedrijven krijgen toegang (e-mailverificatie nog niet actief)
- [x] Eén account per bedrijf (uniek e-mailadres)
- [x] Duidelijke uitleg bij registratie over wat er gedeeld wordt en wat niet
- [x] Bedrijfsprofiel bij registratie (naam, type, regio, inkoopomvang)
- [x] Bedrijfsprofiel bewerkbaar in profiel (inkoopomvang, regio, type)
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
| Datum | Ja | Datum waarop prijs geldt / factuurdatum |
| Opmerking | Nee | Vrij tekstveld |

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
  - Aantal datapunten (hoeveel leden hebben dit product gemeld)
- **Eigen positie**: waar het eigen bedrijf staat t.o.v. de marktrange (onder / binnen / boven)
- **Inkoopomvang**: vergelijkingen binnen dezelfde categorie (klein &lt; €5k/maand, middel €5–20k, groot &gt; €20k)
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
- [x] Minimaal 3 datapunten voor marktdata (privacy)
- [x] Eigen prijs uitgesloten bij marktpositie (geen zelfvergelijking)
- [x] Alleen geaggregeerde data zichtbaar; geen individuele bedrijven
- [x] Filters op vergelijking (groothandel, periode 30/90/365 dagen)
- [x] Dashboard-samenvatting: producten boven marktrange met directe links
- [x] Publieke preview zonder login (`/compare`) met gebluurde prijsdata en aanmeld-CTA
- [ ] Trend over tijd — Fase 3c
- [ ] Export van vergelijkingsrapport (PDF) — Fase 4

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

---

### 4.8 Anonimiteit & privacy

**Doel:** Garanderen dat gedeelde data nooit tot individuele bedrijven herleidbaar is.

**Status:** 🔲 Nog te bouwen (regels vastgelegd, implementatie volgt)

#### Regels

1. **Minimumdrempel**: aggregaties worden pas getoond bij ≥ 3 unieke bijdragen
2. **Geen ranglijst van bedrijven**: nooit tonen welk bedrijf de laagste/hoogste prijs heeft
3. **Geen ruwe data delen**: leden zien alleen statistieken (gemiddelde, mediaan, min, max, spreiding)
4. **Eigen data**: een lid ziet altijd zijn eigen ingevoerde prijzen volledig
5. **Regio-aggregatie**: locatiegegevens alleen op regionaal niveau, nooit per adres
6. **AVG-compliance**: verwerkersovereenkomst, recht op inzage en verwijdering

#### Acceptatiecriteria

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
| Anonimisering | `AnonymizationService` | ✅ Aggregatie per segment (≥3 datapunten) |
| Prijsvergelijking | `PriceComparisonService` + `/compare` | ✅ Zoeken, marktrange, inkoopsegment |
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

### Bestandsstructuur (huidig)

```
/
├── FUNCTIONALITEIT.md    ← dit document
├── README.md             ← repo-overzicht
├── docs/
│   └── DATAMODEL.md      ← databaseschema & relaties
├── index.html            ← landingspagina (GitHub Pages)
├── css/style.css         ← marketing-styling
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
| **Fase 4** | Onderhandelingsrapporten | 4.7 ← **volgende stap** |
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
| 4. Vergelijken | Zoek product → zie marktrange t.o.v. eigen prijs | ✅ Lokaal (PR #14) |
| 5. Inzicht | Dashboard: welke producten zijn duurder dan markt? | ✅ Lokaal |
| 6. Onderhandelen | PDF-rapport met harde cijfers | 🔲 Fase 4 |
| 7. Productie | App op VPS, echte gebruikers | 🔲 Deploy |

**Volgende stap MVP:** **Fase 4** — onderhandelingsrapport (PDF) met harde cijfers per product/groothandel.

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
