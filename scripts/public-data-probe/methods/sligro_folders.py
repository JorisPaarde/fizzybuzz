"""Sligro digitale folders: linklijst + PDF-download via Publitas."""

from __future__ import annotations

import io
import re

import httpx
from pypdf import PdfReader

from .base import ProbeResult

ACTIES_URL = "https://www.sligro.nl/acties.html"
FOLDER_LINK_RE = re.compile(r"https://folder\.sligro\.nl/[a-zA-Z0-9_-]+")
PDF_LINK_RE = re.compile(r"https://view\.publitas\.com/[^\"']+\.pdf[^\"']*")


def _extract_folder_links(html: str) -> list[str]:
    return sorted(set(FOLDER_LINK_RE.findall(html)))


def _extract_pdf_url(folder_html: str) -> str | None:
    match = PDF_LINK_RE.search(folder_html)
    if not match:
        return None
    return match.group(0).split("?")[0] + "?response-content-disposition=attachment"


def probe(client: httpx.Client, pdf_pages: int = 2) -> ProbeResult:
    result = ProbeResult(
        method_id="sligro_folder_pdf",
        name="Sligro folder PDF (Publitas)",
        success=False,
        requires_account=False,
        notes="Promo-prijzen + art.nrs in PDF; geen EAN in ruwe tekst.",
    )

    try:
        acties = client.get(ACTIES_URL)
        result.http_status = acties.status_code
        acties.raise_for_status()
        folder_links = _extract_folder_links(acties.text)
    except Exception as exc:  # noqa: BLE001
        result.errors.append(f"acties.html: {exc}")
        return result

    if not folder_links:
        result.errors.append("Geen folder-links gevonden op acties.html")
        return result

    result.metadata["folder_links"] = folder_links
    result.record_count = len(folder_links)

    # Test eerste food-folder
    food_link = next((l for l in folder_links if "food" in l), folder_links[0])

    try:
        folder_page = client.get(food_link)
        folder_page.raise_for_status()
        pdf_url = _extract_pdf_url(folder_page.text)
        if not pdf_url:
            result.errors.append(f"Geen PDF-URL op {food_link}")
            return result

        result.metadata["tested_folder"] = food_link
        result.metadata["pdf_url"] = pdf_url

        # Volledige PDF nodig voor parsing (typisch 50–90 MB)
        pdf_response = client.get(pdf_url, timeout=120.0)
        pdf_response.raise_for_status()
        pdf_bytes = pdf_response.content
        result.metadata["pdf_bytes"] = len(pdf_bytes)

        reader = PdfReader(io.BytesIO(pdf_bytes))
        lines: list[str] = []
        for page in reader.pages[:pdf_pages]:
            text = page.extract_text() or ""
            lines.extend(text.splitlines())

        price_lines = [
            ln.strip()
            for ln in lines
            if re.search(r"\d+[.,]\d{2}", ln) and ("Art.nr" in ln or "Per kilo" in ln or "VANAF" in ln)
        ]

        result.has_prices = len(price_lines) > 0
        result.sample = [
            {"raw_line": ln}
            for ln in price_lines[:5]
        ]
        result.metadata["pdf_pages_readable"] = min(pdf_pages, len(reader.pages))
        result.success = result.has_prices

    except Exception as exc:  # noqa: BLE001
        result.errors.append(f"folder/pdf: {exc}")

    return result
