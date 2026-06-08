"""Sligro aanbiedingen via publieke product-overview API (zonder login)."""

from __future__ import annotations

import httpx

from .base import ProbeResult

API_URL = "https://www.sligro.nl/api/product-overview/sligro-nl/nl/promotion/query"
PAGE_URL = "https://www.sligro.nl/aanbiedingen.html"


def probe(client: httpx.Client, max_pages: int = 1) -> ProbeResult:
    result = ProbeResult(
        method_id="sligro_promotions_api",
        name="Sligro aanbiedingen API",
        success=False,
        requires_account=False,
        notes="Productcatalogus promo-assortiment; prijzen niet in API-response zonder login.",
    )

    try:
        response = client.get(API_URL, params={"currentPage": 0})
        result.http_status = response.status_code
        response.raise_for_status()
        data = response.json()
    except Exception as exc:  # noqa: BLE001
        result.errors.append(str(exc))
        return result

    products = data.get("products", [])
    pagination = data.get("pagination", {})
    result.record_count = pagination.get("totalResults", len(products))
    result.has_ean = any(p.get("gtin") for p in products)
    result.has_prices = any(
        key for p in products for key in p if "price" in key.lower()
    )

    result.sample = [
        {
            "code": p.get("code"),
            "name": p.get("name"),
            "gtin": p.get("gtin"),
            "packaging": p.get("contentDescription"),
            "brand": p.get("brandName"),
            "url": f"https://www.sligro.nl{p.get('url', '')}",
        }
        for p in products[:3]
    ]

    result.metadata = {
        "api_url": API_URL,
        "page_url": PAGE_URL,
        "total_pages": pagination.get("totalPages"),
        "page_size": pagination.get("pageSize"),
        "max_pages_tested": max_pages,
    }
    result.success = len(products) > 0

    return result
