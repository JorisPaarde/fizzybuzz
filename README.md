# Prijsplein

Homepage for **Prijsplein**, a platform that helps horeca businesses compare the prices they pay across different wholesalers.

> **Productdocumentatie:** alle functionaliteit staat in [`FUNCTIONALITEIT.md`](FUNCTIONALITEIT.md). Agents en ontwikkelaars lezen dit bestand eerst.

## What it does

- Members upload the prices they pay to their wholesalers
- Prices are shared as collective knowledge across the member network
- Members use this insight to negotiate competitive pricing
- The goal: end opaque pricing and achieve lower net prices for everyone

## Live site

After merging to `block3_joris`, the site is published automatically via GitHub Actions:

**https://jorispaarde.github.io/fizzybuzz/**

### One-time repo setup (required)

GitHub Pages must be enabled once in the repository settings:

1. Open [github.com/JorisPaarde/fizzybuzz/settings/pages](https://github.com/JorisPaarde/fizzybuzz/settings/pages)
2. Under **Build and deployment**, set **Source** to **GitHub Actions**
3. Go to [Actions → Deploy to GitHub Pages](https://github.com/JorisPaarde/fizzybuzz/actions/workflows/pages.yml) and click **Run workflow** (or push any commit to `block3_joris`)

After the workflow succeeds, the site is live at the URL above.

## Local preview

```bash
npm start
```

Then open [http://localhost:3000](http://localhost:3000).
