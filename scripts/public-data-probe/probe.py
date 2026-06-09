#!/usr/bin/env python3
"""
Prijsplein — publieke databron-probe (geen accounts).

Gebruik:
    pip install -r requirements.txt
    python probe.py
    python probe.py --json results/latest.json
"""

from __future__ import annotations

import argparse
import json
import sys
from datetime import UTC, datetime
from pathlib import Path

import httpx

from methods.blocked_sites import probe as probe_blocked
from methods.hanos_occ import probe as probe_hanos
from methods.open_food_facts import probe as probe_off
from methods.sligro_folders import probe as probe_sligro_folders
from methods.sligro_promotions import probe as probe_sligro_promotions

DEFAULT_HEADERS = {
    "User-Agent": "PrijspleinPublicProbe/1.0 (+https://github.com/JorisPaarde/fizzybuzz)",
    "Accept-Language": "nl-NL,nl;q=0.9",
    "Accept": "application/json, text/html, */*",
}


def run_all(timeout: float = 120.0) -> dict:
    probes = [
        probe_sligro_promotions,
        probe_sligro_folders,
        probe_hanos,
        probe_off,
        probe_blocked,
    ]

    results = []
    with httpx.Client(
        headers=DEFAULT_HEADERS,
        timeout=timeout,
        follow_redirects=True,
    ) as client:
        for probe_fn in probes:
            print(f"→ {probe_fn.__module__}...", flush=True)
            results.append(probe_fn(client).to_dict())

    successful = [r for r in results if r["success"]]
    with_prices = [r for r in successful if r["has_prices"]]

    return {
        "probed_at": datetime.now(UTC).isoformat(),
        "summary": {
            "total_methods": len(results),
            "successful": len(successful),
            "with_prices": len(with_prices),
            "best_sources": [r["method_id"] for r in with_prices],
        },
        "results": results,
    }


def print_report(report: dict) -> None:
    print()
    print("=" * 60)
    print("PUBLIEKE DATABRONNEN — RESULTATEN")
    print("=" * 60)
    summary = report["summary"]
    print(
        f"Methodes: {summary['successful']}/{summary['total_methods']} succesvol, "
        f"{summary['with_prices']} met prijzen"
    )
    print()

    for result in report["results"]:
        status = "✅" if result["success"] else "❌"
        prices = "💰" if result["has_prices"] else "  "
        ean = "🏷️" if result["has_ean"] else "  "
        print(f"{status} {prices} {ean} {result['name']} ({result['method_id']})")
        if result["record_count"] is not None:
            print(f"     Records: {result['record_count']}")
        if result["notes"]:
            print(f"     {result['notes']}")
        if result["errors"]:
            print(f"     Fout: {'; '.join(result['errors'])}")
        if result["sample"]:
            print(f"     Voorbeeld: {result['sample'][0]}")
        print()

    print("Aanbevolen zonder account:")
    for method_id in summary.get("best_sources", []):
        print(f"  • {method_id}")
    print()


def main() -> int:
    parser = argparse.ArgumentParser(description="Test publieke horeca-databronnen")
    parser.add_argument(
        "--json",
        type=Path,
        default=Path(__file__).parent / "results" / "latest.json",
        help="Pad voor JSON-rapport",
    )
    parser.add_argument("--timeout", type=float, default=120.0)
    args = parser.parse_args()

    report = run_all(timeout=args.timeout)
    print_report(report)

    args.json.parent.mkdir(parents=True, exist_ok=True)
    args.json.write_text(json.dumps(report, indent=2, ensure_ascii=False) + "\n")
    print(f"Rapport opgeslagen: {args.json}")

    return 0 if report["summary"]["successful"] > 0 else 1


if __name__ == "__main__":
    sys.exit(main())
