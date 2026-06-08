"""Open Food Facts — publieke EAN/product-metadata (geen groothandelsprijzen)."""

from __future__ import annotations

import httpx

from .base import ProbeResult

SEARCH_URL = "https://world.openfoodfacts.org/cgi/search.pl"
PRODUCT_URL = "https://world.openfoodfacts.org/api/v2/product/{ean}.json"


def probe(client: httpx.Client) -> ProbeResult:
    result = ProbeResult(
        method_id="open_food_facts",
        name="Open Food Facts API",
        success=False,
        requires_account=False,
        notes="EAN-verrijking en productnamen; geen inkoopprijzen.",
    )

    products: list[dict] = []

    try:
        response = client.get(
            SEARCH_URL,
            params={
                "search_terms": "coca cola",
                "countries_tags_en": "netherlands",
                "page_size": 3,
                "json": 1,
            },
        )
        result.http_status = response.status_code
        if response.is_success:
            data = response.json()
            products = data.get("products", [])
            result.record_count = data.get("count", len(products))
        else:
            result.errors.append(f"search HTTP {response.status_code}")
    except Exception as exc:  # noqa: BLE001
        result.errors.append(f"search: {exc}")

    result.has_ean = bool(products)
    result.has_prices = False

    result.sample = [
        {
            "ean": p.get("code"),
            "name": p.get("product_name"),
            "brands": p.get("brands"),
            "quantity": p.get("quantity"),
        }
        for p in products[:3]
    ]

    # Fallback: directe EAN-lookup (stabieler dan search)
    sligro_ean = "8710398307583"
    try:
        lookup = client.get(PRODUCT_URL.format(ean=sligro_ean))
        lookup.raise_for_status()
        product = lookup.json().get("product", {})
        if product:
            result.sample.append(
                {
                    "ean": sligro_ean,
                    "name": product.get("product_name"),
                    "brands": product.get("brands"),
                    "source": "sligro_gtin_crossref",
                }
            )
            result.has_ean = True
            if result.record_count is None:
                result.record_count = 1
    except Exception as exc:  # noqa: BLE001
        result.errors.append(f"ean lookup: {exc}")

    result.metadata["search_url"] = SEARCH_URL
    result.success = bool(result.sample)

    return result
