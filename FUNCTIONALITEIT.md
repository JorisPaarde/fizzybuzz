# Prijsplein — Functioneel overzicht

> **Dit document is de enige bron van waarheid voor productfunctionaliteit.**
> Elke agent die aan dit project werkt, leest dit bestand eerst en werkt alleen aan functionaliteit die hierin staat beschreven — of werkt dit document bij vóór implementatie van nieuwe features.

**Laatste update:** juni 2026  
**Versie document:** 1.2  
**Live site:** https://jorispaarde.github.io/fizzybuzz/  
**Taal product:** Nederlands (NL)

---

## 1. Productvisie

**Prijsplein** is een platform waar horecabedrijven (restaurants, cafés, hotels en vergelijkbare ondernemingen) de inkoopprijzen die zij betalen bij groothandels met elkaar vergelijken.

### Kernprobleem

Groothandels hanteren **ondoorzichtige, klantspecifieke prijzen**. Twee vergelijkbare horecabedrijven kunnen voor hetzelfde product tot wel 30% prijsverschil betalen, zonder dat ze dit weten. Zonder benchmark is onderhandelen met leveranciers vrijwel onmogelijk.

### Oplossing

Leden **uploaden de prijzen die zij betalen**. Die gegevens worden **geaggreerd en anoniem gedeeld** als collectieve marktkennis. Daarmee krijgt elk lid:

- inzicht in wat de markt betaalt (per product, per groothandel)
- een sterke onderhandelingspositie met harde cijfers
- structureel lagere inkoopkosten

### Missie

Een einde maken aan ondoorzichtige prijzen in de horeca-inkoop, zodat **alle leden netto lagere prijzen** betalen.

### Kernprincipes

| Principe | Betekenis |
|---|---|
| **Geven en nemen** | Wie prijzen deelt, krijgt toegang tot gedeelde kennis |
| **Anonimiteit** | Individuele bedrijven zijn nooit herleidbaar in gedeelde data |
| **Collectieve kracht** | Meer leden = completer beeld = sterkere positie voor iedereen |
| **Transparantie** | Geen verborgen tarieven; marktprijzen worden zichtbaar |
| **Praktisch nut** | Alles wat gebouwd wordt, moet direct helpen bij onderhandelen |

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
| **Lid** | Geregistreerd horecabedrijf; kan prijzen uploaden en vergelijken | 🟡 Basis (registratie + dashboard) |
| **Beheerder** | Platformbeheer; moderatie, datakwaliteit, gebruikersbeheer | 🔲 Nog te bouwen |

---

## 4. Functionele modules

Onderstaande modules beschrijven de volledige beoogde functionaliteit. Per module staat de **status** vermeld.

**Legenda:** ✅ Geïmplementeerd · 🟡 Deels · 🔲 Nog te bouwen

---

### 4.1 Landingspagina (marketing)

**Doel:** Uitleggen wat Prijsplein is en bezoekers overtuigen om lid te worden.

**Status:** ✅ Geïmplementeerd (`index.html`)

#### Secties

| Sectie | Inhoud |
|---|---|
| Header | Logo, navigatie (Hoe het werkt, Waarom, Voordelen), CTA "Word lid" |
| Hero | Waardepropositie + visuele prijsvergelijkingskaart (voorbeelddata) |
| Probleem | Drie pijnpunten: ondoorzichtige prijzen, geen vergelijkingspunt, stijgende kosten |
| Hoe het werkt | Drie stappen: uploaden → vergelijken → onderhandelen |
| Voordelen | Vier voordelen + quote + statistieken |
| Product | Wat Prijsplein doet: delen → anonimiseren → ontvangen |
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
  - Aantal medewerkers / omvang (optioneel, voor segmentatie)
- Akkoord met voorwaarden, met nadruk op anonimiteitsregels
- Inloggen / uitloggen
- Wachtwoord vergeten

#### Acceptatiecriteria

- [ ] Alleen geverifieerde horecabedrijven krijgen toegang (e-mailverificatie nog niet actief)
- [x] Eén account per bedrijf (uniek e-mailadres)
- [x] Duidelijke uitleg bij registratie over wat er gedeeld wordt en wat niet
- [x] Bedrijfsprofiel bij registratie (naam, type, regio)
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

**Status:** 🔲 Nog te bouwen

#### Functionaliteit

- **Zoeken** op productnaam
- **Vergelijken** per product:
  - Laagste, hoogste en gemiddelde ledenprijs
  - Prijs per groothandel (geaggregeerd, anoniem)
  - Aantal datapunten (hoeveel leden hebben dit product gemeld)
- **Eigen positie**: waar het eigen bedrijf staat t.o.v. het gemiddelde (zonder andere individuele bedrijven te tonen)
- **Filters**:
  - Groothandel
  - Productcategorie
  - Regio (geaggregeerd)
  - Periode (laatste 30/90/365 dagen)

#### Weergave

- Tabel- en kaartweergave
- Visuele indicatie: groen (onder gemiddelde), rood (boven gemiddelde)
- Trend over tijd (indien voldoende data)

#### Acceptatiecriteria

- [ ] Geen individueel herleidbare data zichtbaar voor andere leden
- [ ] Minimaal 3 datapunten nodig voordat een aggregatie getoond wordt (privacy)
- [ ] Eigen prijzen altijd volledig zichtbaar voor het eigen bedrijf
- [ ] Export van vergelijkingsrapport (PDF) voor onderhandelingsgesprekken

---

### 4.5 Groothandelsbeheer

**Doel:** Overzicht van groothandels waar leden inkopen.

**Status:** 🔲 Nog te bouwen

#### Functionaliteit

- Standaardlijst met bekende groothandels (Sligro, Bidfood, Hanos, etc.)
- Leden kunnen groothandel toevoegen als deze niet in de lijst staat
- Per groothandel: gemiddelde ledenprijs per productcategorie
- Koppeling met eigen prijsupload

#### Acceptatiecriteria

- [ ] Groothandels zijn normaliseerd (geen duplicaten door spelfouten)
- [ ] Beheerder kan groothandels samenvoegen en modereren

---

### 4.6 Productcatalogus

**Doel:** Gestandaardiseerde productnamen voor betrouwbare vergelijking.

**Status:** 🔲 Nog te bouwen

#### Functionaliteit

- Centrale productdatabase met categorieën (groenten, vlees, zuivel, dranken, etc.)
- Fuzzy matching bij upload: "tomaat cherry 5kg" → "Tomaten, cherry"
- Suggesties bij handmatige invoer
- Nieuwe producten kunnen worden voorgesteld door leden

#### Acceptatiecriteria

- [ ] Producten zijn vergelijkbaar over leden heen
- [ ] Eenheden worden genormaliseerd (alles omgerekend naar basiseenheid waar mogelijk)
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
  ├── id, naam, categorie, standaard_eenheid
  └── (geaggregeerde statistieken)

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
| Anonimisering | `AnonymizationService` | 🟡 Skelet (≥3 datapunten) |
| Prijsupload | Handmatig + foto/PDF + review | 🟡 Lokaal |
| AI-extractie | OpenAI `gpt-4o-mini` | 🟡 Via `OPENAI_API_KEY` |
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
├── index.html            ← landingspagina (GitHub Pages)
├── css/style.css         ← marketing-styling
├── app/                  ← Laravel-applicatie
│   ├── app/Models/       ← User, Product, Wholesaler, PriceSubmission, AggregatedPrice
│   ├── app/Services/     ← AnonymizationService
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

---

## 10. Ontwerprichtlijnen

### Visuele identiteit (huidige landingspagina)

| Element | Waarde |
|---|---|
| Achtergrond | Donker (#0f1419) |
| Accent | Amber/goud (#e8a838) |
| Tekst | Licht (#e8e4dc) |
| Display-font | Fraunces (serif) |
| Body-font | DM Sans (sans-serif) |
| Toon | Professioneel, betrouwbaar, direct — geen jargon |

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
| **Fase 3** | Prijsvergelijking | 4.4, 4.5, 4.6 |
| **Fase 4** | Onderhandelingsrapporten | 4.7 |
| **Fase 5** | Bestandsupload (prijslijsten/facturen) | 4.3 (bestand) |
| **Fase 6** | Notificaties | 4.9 |
| **Fase 7** | Beheer & moderatie | 4.10 |

> **Aanpassingen aan deze roadmap:** altijd eerst dit document bijwerken, daarna implementeren.

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
