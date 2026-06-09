"""HANOS SAP Commerce OCC API — publiek zonder login."""

from __future__ import annotations

import httpx

from .base import ProbeResult

BASE = "https://api.hanos.nl/occ/v2/hanos-nl"
SEARCH_URL = f"{BASE}/products/search"


def probe(client: httpx.Client, page_size: int = 5) -> ProbeResult:
    result = ProbeResult(
        method_id="hanos_occ_api",
        name="HANOS OCC product-API",
        success=False,
        requires_account=False,
        notes="27k+ producten met prijzen en actie-info; EAN niet in search-response.",
    )

    try:
        response = client.get(
            SEARCH_URL,
            params={"query": ":relevance", "pageSize": page_size},
        )
        result.http_status = response.status_code
        response.raise_for_status()
        data = response.json()
    except Exception as exc:  # noqa: BLE001
        result.errors.append(str(exc))
        return result

    products = data.get("products", [])
    pagination = data.get("pagination", {})
    result.record_count = pagination.get("totalResults", len(products))
    result.has_prices = any(p.get("price", {}).get("value") for p in products)
    result.has_ean = any(p.get("ean") or p.get("gtin") for p in products)

    result.sample = []
    for product in products[:3]:
        price = product.get("price", {})
        result.sample.append(
            {
                "code": product.get("code"),
                "name": product.get("name"),
                "price": price.get("formattedValue"),
                "promotion": price.get("promotion"),
                "old_price": price.get("formattedOldPrice"),
                "packaging": product.get("packagingUnit"),
                "manufacturer": product.get("manufacturer"),
                "url": f"https://www.hanos.nl{product.get('url', '')}",
            }
        )

    # Detail-endpoint: meer prijsinfo (actiedata)
    if products:
        code = products[0]["code"]
        try:
            detail = client.get(f"{BASE}/products/{code}", params={"fields": "FULL"})
            detail.raise_for_status()
            detail_data = detail.json()
            result.metadata["detail_price_fields"] = list(
                detail_data.get("price", {}).keys()
            )
            result.sample[0]["detail_promotion"] = detail_data.get("price", {}).get(
                "promotionLabel"
            )
        except Exception as exc:  # noqa: BLE001
            result.errors.append(f"detail: {exc}")

    result.metadata["api_base"] = BASE
    result.metadata["total_pages"] = pagination.get("totalPages")
    result.success = result.has_prices and len(products) > 0

    return result
