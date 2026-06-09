# PriceSignal — Homepage structuur & uitgangspunten

> Marketing landingspagina: `index.html` · Live: https://jorispaarde.github.io/fizzybuzz/  
> Functionele context: [`FUNCTIONALITEIT.md`](../FUNCTIONALITEIT.md) §4.1  
> **Laatste update:** juni 2026 · **Versie:** 1.0

---

## Uitgangspunten

De homepage moet een drukke horecaondernemer in **30 seconden** laten begrijpen:

1. **Wat is dit?** — Prijsinzicht: jouw inkoopprijzen vs. de markt
2. **Wat levert het mij op?** — Minder inkoop, onderhandelen met cijfers, betere marge
3. **Wat moet ik doen?** — Lid worden (primair); optioneel eerst rondkijken

### Copy-principes (Rory Sutherland)

| Principe | Toepassing op homepage |
|---|---|
| **Reframing** | Het probleem is niet de prijs — het is informatie-asymmetrie (“Je onderhandelt blind”) |
| **Reciprociteit** | Geven = ontvangen; je betaalt die prijzen al, nu worden ze nuttig |
| **Loss aversion** | Kosten van geen inzicht — stilletjes te veel betalen |
| **Lage drempel** | Factuur uploaden in 2 min; geen spreadsheet |
| **Bezwaren weerleggen** | Vertrouwelijkheid, tijd, “ik geef te veel weg” — expliciete reframe-cards |
| **Social proof** | Netwerkeffect, stats, collectieve intelligentie |
| **Signaling** | Trust bar, anonimiteit, AVG, alleen horeca |

### Wat we bewust níet doen

- **Geen tour als hoofdverhaal** — `rondleiding.html` is secundair (link in hero + CTA), geen volledige homepage-sectie
- **Geen dubbele secties** — één “hoe het werkt”, geen aparte product- én stappen-sectie
- **Geen abstract jargon** vóór concrete opbrengst — eerst wat je wint, dan mechaniek
- **Geen primaire CTA naar rondleiding** — “Word lid” is altijd primair

### Tone of voice

- Spreek de ondernemer direct aan (“jij”, “jouw”)
- Kort, concreet, praktisch voordeel
- Corporate en betrouwbaar — geen hype, geen buzzwords
- Nederlands

---

## Paginastructuur (vaste volgorde)

De volgorde is **normatief**. Wijzig alleen na bewuste beslissing en update dit document + `FUNCTIONALITEIT.md` §4.1.

| # | Sectie | HTML `id` | Doel voor bezoeker |
|---|--------|-------------|-------------------|
| 1 | Header + trust bar | — | Navigatie, vertrouwenssignalen |
| 2 | Hero | `#wat-het-doet` (pitch-strip) | Reframe + in één oogopslag wat het is |
| 3 | Wat je wint | `#opbrengst` | Concrete opbrengst (geld, onderhandelen, marge) |
| 4 | Hoe het werkt | `#hoe-het-werkt` | Drie stappen: deel → vergelijk → onderhandel |
| 5 | Waarom delen | `#waarom-delen` | Uitwisseling + bezwaren weerlegd |
| 6 | Kosten van niets doen | `#kosten` | Waarom wachten geld kost |
| 7 | Vertrouwen | `#vertrouwen` | Anonimiteit, netwerk, statistieken |
| 8 | Aanmelden | `#aanmelden` | Conversie — lid worden |
| 9 | Footer | — | Tagline, merk |

### Navigatie (header)

| Link | Anker |
|---|---|
| Wat je wint | `#opbrengst` |
| Hoe het werkt | `#hoe-het-werkt` |
| Waarom delen | `#waarom-delen` |
| Word lid (CTA) | `#aanmelden` |

Rondleiding staat **niet** in de hoofdnavigatie.

### Secundaire pagina's

| Pagina | Rol |
|---|---|
| `rondleiding.html` | Interactieve demo met voorbeelddata; link vanuit hero (secundaire knop) en CTA-footnote |
| `css/style.css` | Styling; cache-bust via `?v=` in HTML |

---

## Sectie-inhoud (referentie)

### 2. Hero

- **Eyebrow:** Voor restaurants, cafés en hotels
- **H1:** Je onderhandelt blind. Je leverancier niet.
- **Lead:** Groothandels hebben alle data; jij alleen je factuur. PriceSignal = anoniem delen + markt zien.
- **Pitch-strip:** Jij deelt · Wij vergelijken · Jij wint
- **Visual:** Prijsvergelijkingskaart (voorbeelddata tomaten)
- **CTA primair:** Word lid — gratis
- **CTA secundair:** Eerst even rondkijken → `rondleiding.html`

### 3. Wat je wint

Drie kaarten:

1. Minder inkoopkosten
2. Onderhandelen met cijfers
3. Betere marge

### 4. Hoe het werkt

Drie genummerde stappen (geen duplicaat elders):

1. Deel wat jij betaalt (foto/PDF/e-mail/handmatig)
2. Zie wat de markt betaalt (anoniem, met peildatum)
3. Onderhandel met bewijs

### 5. Waarom delen

- Uitwisselingsblok: jij geeft / jij krijgt
- Drie reframe-cards: vertrouwelijkheid, te veel weggeven, geen tijd

### 6. Kosten van niets doen

Drie pijnpunten: elke prijs voelt normaal, zij hebben de data, marge begint bij inkoop

### 7. Vertrouwen

- Anonimiteit, geven=ontvangen, meetbaar resultaat
- Quote + stats (2 min upload, 30% verschil)

### 8. Aanmelden

- E-mailformulier (placeholder)
- Footnote: link naar rondleiding voor twijfelaars

---

## Visuele identiteit

| Element | Waarde |
|---|---|
| Achtergrond | `#f7f9fc` |
| Primair | Corporate navy `#0b2545` |
| Accent | Trust blue `#2e6ba8` |
| Font | Source Sans 3 |
| Stijl | Corporate, betrouwbaar, mobile-first |

Zie ook `FUNCTIONALITEIT.md` §10.

---

## Checklist bij wijzigingen

- [ ] Volgorde secties behouden (tenzij dit document wordt bijgewerkt)
- [ ] “Word lid” blijft primaire CTA in hero en header
- [ ] Rondleiding maximaal secundair (geen eigen homepage-sectie)
- [ ] Geen dubbele “hoe het werkt”-content
- [ ] `css/style.css?v=` cache-bust bumpen bij CSS-wijziging
- [ ] `package.json` versie bumpen bij marketing-release
- [ ] `FUNCTIONALITEIT.md` §4.1 synchroniseren

---

*Wijzig homepage-structuur altijd hier én in `FUNCTIONALITEIT.md` §4.1.*
