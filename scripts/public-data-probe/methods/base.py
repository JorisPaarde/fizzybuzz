from __future__ import annotations

from dataclasses import dataclass, field
from typing import Any


@dataclass
class ProbeResult:
    method_id: str
    name: str
    success: bool
    requires_account: bool = False
    http_status: int | None = None
    record_count: int | None = None
    has_prices: bool = False
    has_ean: bool = False
    notes: str = ""
    sample: list[dict[str, Any]] = field(default_factory=list)
    errors: list[str] = field(default_factory=list)
    metadata: dict[str, Any] = field(default_factory=dict)

    def to_dict(self) -> dict[str, Any]:
        return {
            "method_id": self.method_id,
            "name": self.name,
            "success": self.success,
            "requires_account": self.requires_account,
            "http_status": self.http_status,
            "record_count": self.record_count,
            "has_prices": self.has_prices,
            "has_ean": self.has_ean,
            "notes": self.notes,
            "sample": self.sample,
            "errors": self.errors,
            "metadata": self.metadata,
        }
