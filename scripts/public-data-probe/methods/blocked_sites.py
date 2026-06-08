"""Sites die zonder account (en vaak zonder browser) geblokkeerd zijn."""

from __future__ import annotations

import httpx

from .base import ProbeResult

SITES = [
    ("bidfood", "https://www.bidfood.nl"),
    ("makro", "https://www.makro.nl/nl/aanbiedingen"),
]


def probe(client: httpx.Client) -> ProbeResult:
    result = ProbeResult(
        method_id="blocked_wholesalers",
        name="Bidfood / Makro (publieke toegang)",
        success=False,
        requires_account=False,
        notes="Cloudflare/WAF blokkeert server-requests; Playwright + account waarschijnlijk nodig.",
    )

    outcomes: list[dict] = []

    for name, url in SITES:
        try:
            response = client.get(url)
            outcomes.append(
                {
                    "site": name,
                    "url": url,
                    "status": response.status_code,
                    "accessible": response.status_code == 200,
                }
            )
        except Exception as exc:  # noqa: BLE001
            outcomes.append({"site": name, "url": url, "error": str(exc)})

    result.metadata["sites"] = outcomes
    result.record_count = sum(1 for o in outcomes if o.get("accessible"))
    result.success = result.record_count > 0
    result.http_status = outcomes[0].get("status") if outcomes else None

    return result
