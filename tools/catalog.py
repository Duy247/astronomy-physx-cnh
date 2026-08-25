#!/usr/bin/env python3
"""Deterministic catalog migration, generation, and integrity checks."""

from __future__ import annotations

import argparse
import html as html_module
import json
import re
import shutil
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CARD_DIR = ROOT / "image_card"
DATA_DIR = CARD_DIR / "card_data"
GALLERY_DIR = ROOT / "gallery"

CATEGORIES = {
    "gallery_178ed.json": "178ed",
    "gallery_500mm.json": "500mm",
    "gallery_clusters.json": "clusters",
    "gallery_fits.json": "fits",
    "gallery_galaxies.json": "galaxies",
    "gallery_nebulae.json": "nebulae",
    "gallery_newCCD.json": "newccd",
    "gallery_solarsystem_moon.json": "solar-system-moon",
    "gallery_solarsystem.json": "solar-system",
}

CATEGORY_PAGES = {
    "178ED.php": ("gallery_178ed.json", "7-inch Refractor Images"),
    "500mm.php": ("gallery_500mm.json", "500mm Lens Images"),
    "Clusters.php": ("gallery_clusters.json", "Star Clusters"),
    "Galaxies.php": ("gallery_galaxies.json", "Galaxies"),
    "Nebulae.php": ("gallery_nebulae.json", "Nebulae"),
    "SolarSystem.php": ("gallery_solarsystem.json", "Solar System"),
    "SolarSystem_Moon.php": ("gallery_solarsystem_moon.json", "The Moon"),
    "index_fits.php": ("gallery_fits.json", "Unprocessed FITS Image Files"),
    "index_newCCD.php": ("gallery_newCCD.json", "Latest Images"),
}

LINK_REPLACEMENTS = {
    "Equipment_PhotoGuide.htm": "/Equipment_PhotoGuide.php",
    "Equipment_PhotoGuide.php": "/Equipment_PhotoGuide.php",
    "Equipment_RC32.htm": "/Equipment_RC32.php",
    "Process_m13.php": "/Process_m13.php",
}


def load_json(path: Path):
    return json.loads(path.read_text(encoding="utf-8"))


def write_json(path: Path, value) -> None:
    temporary = path.with_suffix(path.suffix + ".tmp")
    temporary.write_text(json.dumps(value, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
    json.loads(temporary.read_text(encoding="utf-8"))
    temporary.replace(path)


def normalized_local_path(url: str, base: Path = ROOT) -> Path | None:
    if not url or re.match(r"^(?:https?:|mailto:|tel:|#|data:)", url, re.I):
        return None
    clean = url.split("?", 1)[0].split("#", 1)[0].lstrip("/")
    return base / Path(clean)


def has_exact_case(path: Path) -> bool:
    try:
        relative = path.resolve().relative_to(ROOT.resolve())
    except ValueError:
        return True
    current = ROOT
    for part in relative.parts:
        try:
            names = {child.name for child in current.iterdir()}
        except OSError:
            return False
        if part not in names:
            return False
        current /= part
    return True


def structured_details(rows: list[dict]) -> list[dict]:
    result = []
    for row in rows:
        if "text" in row or "links" in row:
            result.append(row)
            continue
        value = str(row.get("value", ""))
        links = []
        for href, label_html in re.findall(r'<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)</a>', value, re.I | re.S):
            label = re.sub(r"<[^>]+>", " ", label_html)
            links.append({"href": href, "label": re.sub(r"\s+", " ", html_module.unescape(label)).strip() or href})
        text = re.sub(r"<br\s*/?>|</li>|</p>", "\n", value, flags=re.I)
        text = re.sub(r"<[^>]+>", " ", text)
        text = html_module.unescape(text)
        text = "\n".join(re.sub(r"\s+", " ", line).strip() for line in text.splitlines() if line.strip())
        result.append({"label": str(row.get("label", "Detail")), "text": text, "links": links})
    return result


def gallery_records(from_checkpoint: bool = False) -> dict[str, list[dict]]:
    result: dict[str, list[dict]] = {}
    for filename, category in CATEGORIES.items():
        if from_checkpoint:
            raw = subprocess.check_output(["git", "show", f"HEAD:gallery/{filename}"], cwd=ROOT, text=True)
            items = json.loads(raw)
        else:
            items = load_json(GALLERY_DIR / filename)
        seen = set()
        for order, item in enumerate(items):
            card_id = Path(item.get("link", "")).stem
            if card_id and card_id.lower() not in seen:
                seen.add(card_id.lower())
                result.setdefault(card_id.lower(), []).append({"category": category, "order": order, **item})
    return result


def normalize_data(apply: bool) -> int:
    mismatch = DATA_DIR / "site_data_pk318p41.json"
    corrected = DATA_DIR / "site_data_Nebulae_pk318p41.json"
    if mismatch.exists() and not corrected.exists():
        print(f"rename {mismatch.relative_to(ROOT)} -> {corrected.relative_to(ROOT)}")
        if apply:
            mismatch.replace(corrected)

    records = gallery_records(from_checkpoint=True)
    changed = 0
    for path in sorted(DATA_DIR.glob("site_data_*.json")):
        card_id = path.stem.removeprefix("site_data_")
        data = load_json(path)
        memberships = data.get("galleries") or records.get(card_id.lower(), [])
        primary = memberships[0] if memberships else {"category": "uncategorized", "order": 9999}
        gallery = {key: primary.get(key, "") for key in ("title", "subtitle", "thumb", "alt")}
        image = data.get("image", {})
        if image.get("large") in ("", "/") and image.get("thumb"):
            preferred = image["thumb"].replace("_800.", "_2000.").replace("_100.", "_2000.")
            preferred_path = normalized_local_path(preferred)
            image["large"] = preferred if preferred_path and preferred_path.is_file() else image["thumb"]
        normalized = {
            **data,
            "id": card_id,
            "category": primary["category"],
            "order": primary["order"],
            "canonical_url": f"/image_card/{card_id}.php",
            "gallery": {
                "title": gallery.get("title", data.get("object", card_id)),
                "subtitle": gallery.get("subtitle", data.get("object", card_id)),
                "thumb": gallery.get("thumb", data.get("image", {}).get("thumb", "")),
                "alt": gallery.get("alt", data.get("image", {}).get("alt", "")),
            },
            "galleries": memberships,
            "image": image,
            "details": structured_details(data.get("details", [])),
        }
        raw = json.dumps(normalized, indent=2, ensure_ascii=False) + "\n"
        if raw != path.read_text(encoding="utf-8"):
            changed += 1
            if apply:
                write_json(path, normalized)
    print(f"normalized records: {changed}")
    return changed


def fix_links(apply: bool) -> int:
    changed = 0
    for path in sorted(DATA_DIR.glob("*.json")):
        text = path.read_text(encoding="utf-8")
        updated = text
        for old, new in LINK_REPLACEMENTS.items():
            updated = updated.replace(f'href=\\"{old}', f'href=\\"{new}')
        if updated != text:
            changed += 1
            print(f"repair links: {path.relative_to(ROOT)}")
            if apply:
                json.loads(updated)
                path.write_text(updated, encoding="utf-8")
    print(f"files with repaired links: {changed}")
    return changed


def fix_php_links(apply: bool) -> int:
    changed = 0
    pattern = re.compile(r'href=(["\'])(?!https?:|mailto:|tel:|#|/)([^"\']+)\.htm\1', re.I)
    for path in sorted(ROOT.glob("*.php")):
        text = path.read_text(encoding="utf-8")
        updated = pattern.sub(lambda match: f'href={match.group(1)}{match.group(2)}.php{match.group(1)}', text)
        if updated != text:
            changed += 1
            print(f"repair PHP links: {path.name}")
            if apply:
                path.write_text(updated, encoding="utf-8")
    print(f"PHP files with repaired links: {changed}")
    return changed


def refactor_wrappers(apply: bool) -> int:
    changed = 0
    for path in sorted(CARD_DIR.glob("*.php")):
        text = path.read_text(encoding="utf-8")
        match = re.search(r"card_data/([^'\"]+\.json)", text)
        if not match:
            continue
        json_name = match.group(1)
        if json_name == "site_data_pk318p41.json":
            json_name = "site_data_Nebulae_pk318p41.json"
        renderer_match = re.search(r"astro_render_object\([^;]+,\s*(true|false)\s*\)", text)
        with_sky = renderer_match.group(1) == "true" if renderer_match else "image_card_nosky.php" not in text
        updated = (
            "<?php\n\n"
            "declare(strict_types=1);\n\n"
            "require_once dirname(__DIR__) . '/app/bootstrap.php';\n"
            f"astro_render_object(__DIR__ . '/card_data/{json_name}', {str(with_sky).lower()});\n"
        )
        if updated != text:
            changed += 1
            if apply:
                path.write_text(updated, encoding="utf-8")
    print(f"refactored object wrappers: {changed}")
    return changed


def archive_pages(apply: bool) -> int:
    destination = ROOT / "legacy" / "pages"
    pages = sorted(ROOT.glob("*.htm"))
    for source in pages:
        target = destination / source.name
        print(f"archive {source.name} -> {target.relative_to(ROOT)}")
        if not apply:
            continue
        destination.mkdir(parents=True, exist_ok=True)
        if target.exists():
            raise RuntimeError(f"Archive target already exists: {target}")
        shutil.move(source, target)
        php = ROOT / f"{source.stem}.php"
        if not php.exists():
            php.write_text(
                "<?php\n\ndeclare(strict_types=1);\n\n"
                "require_once __DIR__ . '/app/bootstrap.php';\n"
                f"astro_render_legacy_page('{source.stem}');\n",
                encoding="utf-8",
            )
    print(f"archived root pages: {len(pages)}")
    return len(pages)


def refactor_category_pages(apply: bool) -> int:
    changed = 0
    for filename, (gallery, heading) in CATEGORY_PAGES.items():
        path = ROOT / filename
        updated = (
            "<?php\n\ndeclare(strict_types=1);\n\n"
            "require_once __DIR__ . '/app/bootstrap.php';\n"
            f"astro_render_gallery(__DIR__ . '/gallery/{gallery}', '{heading}');\n"
        )
        if not path.exists() or path.read_text(encoding="utf-8") != updated:
            changed += 1
            print(f"refactor category: {filename}")
            if apply:
                path.write_text(updated, encoding="utf-8")
    print(f"refactored category pages: {changed}")
    return changed


def build_indexes(apply: bool) -> int:
    grouped: dict[str, list[dict]] = {category: [] for category in CATEGORIES.values()}
    for path in sorted(DATA_DIR.glob("site_data_*.json")):
        data = load_json(path)
        memberships = data.get("galleries") or [{"category": data.get("category", "uncategorized"), "order": data.get("order", 9999), **data.get("gallery", {})}]
        for item in memberships:
            category = item.get("category", "uncategorized")
            if category not in grouped:
                continue
            grouped[category].append({"link": data.get("canonical_url", ""), "thumb": item.get("thumb", ""), "alt": item.get("alt", ""), "title": item.get("title", ""), "subtitle": item.get("subtitle", ""), "_order": item.get("order", 9999)})
    static_path = GALLERY_DIR / "static_entries.json"
    if static_path.exists():
        for category, items in load_json(static_path).items():
            if category in grouped:
                grouped[category].extend(items)
    changed = 0
    for filename, category in CATEGORIES.items():
        values = sorted(grouped[category], key=lambda item: (item.pop("_order"), item["link"].lower()))
        path = GALLERY_DIR / filename
        raw = json.dumps(values, indent=2, ensure_ascii=False) + "\n"
        if raw != path.read_text(encoding="utf-8"):
            changed += 1
            print(f"rebuild {path.relative_to(ROOT)}")
            if apply:
                write_json(path, values)
    print(f"rebuilt gallery indexes: {changed}")
    return changed


def build_manifest(apply: bool) -> int:
    fits_references = set()
    image_references = set()
    for path in DATA_DIR.glob("*.json"):
        data = load_json(path)
        image = data.get("image", {})
        for key in ("large", "thumb"):
            if image.get(key):
                image_references.add(Path(image[key]).name.lower())
        for row in data.get("details", []):
            for link in row.get("links", []):
                match = re.search(r"/fits/([^/?#]+\.FIT)", link.get("href", ""), re.I)
                if match:
                    fits_references.add(match.group(1).lower())
    resources = []
    for folder, kinds in (("Images", {".jpg": "image", ".jpeg": "image", ".png": "image", ".mov": "video"}), ("fits", {".fit": "fits"}), ("process", {".jpg": "process-image", ".png": "process-image"})):
        for path in sorted((ROOT / folder).glob("*")):
            if not path.is_file() or path.suffix.lower() not in kinds:
                continue
            key = path.name.lower()
            referenced = key in (fits_references if folder == "fits" else image_references)
            resources.append({"path": path.relative_to(ROOT).as_posix(), "kind": kinds[path.suffix.lower()], "bytes": path.stat().st_size, "referenced_by_catalog": referenced})
    manifest = {"version": 1, "resources": resources}
    target = ROOT / "gallery" / "resource_manifest.json"
    raw = json.dumps(manifest, indent=2, ensure_ascii=False) + "\n"
    changed = not target.exists() or target.read_text(encoding="utf-8") != raw
    if changed and apply:
        write_json(target, manifest)
    print(f"resource manifest entries: {len(resources)}; changed: {changed}")
    return int(changed)


def migrate_page(source_name: str | None, category: str | None, apply: bool) -> int:
    if not source_name or not category:
        raise RuntimeError("migrate-page requires --source and --category")
    source = Path(source_name)
    if not source.is_absolute():
        source = ROOT / source
    source = source.resolve()
    if ROOT.resolve() not in source.parents or not source.is_file() or source.suffix.lower() != ".htm":
        raise RuntimeError("Source must be an existing .htm file inside the project")
    if category not in CATEGORIES.values():
        raise RuntimeError(f"Unknown category: {category}")
    card_id = source.stem
    data_path = DATA_DIR / f"site_data_{card_id}.json"
    php_path = CARD_DIR / f"{card_id}.php"
    if data_path.exists() or php_path.exists():
        raise RuntimeError(f"Refusing to replace existing object: {card_id}")
    html = source.read_text(encoding="utf-8", errors="replace")
    title_match = re.search(r"<title[^>]*>(.*?)</title>", html, re.I | re.S)
    image_match = re.search(r'<a[^>]+href=["\']([^"\']+)["\'][^>]*>\s*<img[^>]+src=["\']([^"\']+)["\'][^>]*alt=["\']?([^"\'>]*)', html, re.I | re.S)
    if not image_match:
        raise RuntimeError("Could not identify the linked primary image")
    object_name = re.sub(r"\s+", " ", (image_match.group(3) or card_id).strip())
    large = "/" + image_match.group(1).lstrip("/")
    thumb = "/" + image_match.group(2).lstrip("/")
    details = []
    for row in re.findall(r"<tr[^>]*>(.*?)</tr>", html, re.I | re.S):
        cells = re.findall(r"<td[^>]*>(.*?)</td>", row, re.I | re.S)
        if len(cells) != 2:
            continue
        label = re.sub(r"<[^>]+>", " ", cells[0])
        label = re.sub(r"\s+", " ", label).strip(" :\r\n\t")
        value = re.sub(r"<(?:script|iframe)[^>]*>.*?</(?:script|iframe)>", "", cells[1], flags=re.I | re.S).strip()
        if label and value:
            details.append({"label": label, "value": value})
    order = sum(1 for path in DATA_DIR.glob("*.json") if load_json(path).get("category") == category)
    gallery_thumb = thumb.replace("_800.", "_100.")
    membership = {"category": category, "order": order, "link": f"/image_card/{card_id}.php", "thumb": gallery_thumb, "alt": object_name, "title": object_name, "subtitle": object_name}
    data = {"id": card_id, "category": category, "order": order, "canonical_url": membership["link"], "gallery": {key: membership[key] for key in ("title", "subtitle", "thumb", "alt")}, "galleries": [membership], "title": re.sub(r"<[^>]+>", "", title_match.group(1)).strip() if title_match else object_name, "object": object_name, "image": {"large": large, "thumb": thumb, "alt": object_name, "survey": "P/DSS2/color", "fov": 25, "target": object_name.split()[0]}, "details": structured_details(details)}
    print(f"create {data_path.relative_to(ROOT)} and {php_path.relative_to(ROOT)}")
    if apply:
        write_json(data_path, data)
        php_path.write_text("<?php\n\ndeclare(strict_types=1);\n\nrequire_once dirname(__DIR__) . '/app/bootstrap.php';\n" f"astro_render_object(__DIR__ . '/card_data/{data_path.name}', true);\n", encoding="utf-8")
        build_indexes(True)
        build_manifest(True)
    return 0


def audit_media() -> int:
    build_manifest(False)
    manifest_path = GALLERY_DIR / "resource_manifest.json"
    if not manifest_path.exists():
        print("Run build-manifest --apply before auditing.")
        return 1
    resources = load_json(manifest_path)["resources"]
    totals = {}
    for item in resources:
        key = (item["kind"], item["referenced_by_catalog"])
        totals[key] = totals.get(key, 0) + 1
    for (kind, referenced), count in sorted(totals.items()):
        print(f"{kind}: referenced={referenced} files={count}")
    return 0


def validate() -> int:
    errors = []
    warnings = []
    data_files = sorted(DATA_DIR.glob("site_data_*.json"))
    php_files = sorted(CARD_DIR.glob("*.php"))
    php_ids = {path.stem.lower() for path in php_files}
    data_ids = set()
    for path in data_files:
        try:
            data = load_json(path)
        except Exception as error:
            errors.append(f"{path.relative_to(ROOT)}: invalid JSON: {error}")
            continue
        card_id = data.get("id", path.stem.removeprefix("site_data_"))
        data_ids.add(str(card_id).lower())
        required = ("id", "category", "order", "canonical_url", "gallery", "galleries", "title", "object", "image", "details")
        missing = [key for key in required if key not in data]
        if missing:
            errors.append(f"{path.name}: missing required fields {', '.join(missing)}")
        if not isinstance(data.get("details"), list) or any(not isinstance(row, dict) or not {"label", "text", "links"}.issubset(row) for row in data.get("details", [])):
            errors.append(f"{path.name}: details do not match the structured schema")
        for key in ("large", "thumb"):
            url = data.get("image", {}).get(key, "")
            local = normalized_local_path(url)
            if url and local and not local.is_file():
                errors.append(f"{path.name}: missing image {url}")
            elif local and local.is_file() and not has_exact_case(local):
                errors.append(f"{path.name}: image path has incorrect case {url}")
        for row in data.get("details", []):
            urls = [link.get("href", "") for link in row.get("links", [])]
            urls.extend(re.findall(r'href=["\']([^"\']+)', row.get("value", ""), re.I))
            for url in urls:
                local = normalized_local_path(url, CARD_DIR if not url.startswith("/") else ROOT)
                if local and not local.is_file():
                    errors.append(f"{path.name}: broken link {url}")
                elif local and local.is_file() and not has_exact_case(local):
                    errors.append(f"{path.name}: link has incorrect case {url}")
    missing_php = sorted(data_ids - php_ids)
    missing_data = sorted(php_ids - data_ids)
    if missing_php:
        errors.append(f"data without PHP wrapper: {', '.join(missing_php)}")
    if missing_data:
        errors.append(f"PHP wrapper without data: {', '.join(missing_data)}")
    gallery_count = 0
    for filename in CATEGORIES:
        seen_links = set()
        for item in load_json(GALLERY_DIR / filename):
            gallery_count += 1
            if not {"link", "thumb", "alt", "title", "subtitle"}.issubset(item):
                errors.append(f"{filename}: gallery record is missing required fields")
            link = item.get("link", "")
            key = link.lower()
            if key in seen_links:
                warnings.append(f"duplicate gallery link: {link}")
            seen_links.add(key)
            for field in ("link", "thumb"):
                local = normalized_local_path(item.get(field, ""))
                if local and not local.is_file():
                    errors.append(f"{filename}: missing {field} {item.get(field)}")
                elif local and local.is_file() and not has_exact_case(local):
                    errors.append(f"{filename}: {field} has incorrect case {item.get(field)}")
    print(f"objects={len(data_files)} wrappers={len(php_files)} gallery_records={gallery_count}")
    for warning in warnings:
        print(f"WARNING: {warning}")
    for error in errors:
        print(f"ERROR: {error}")
    print(f"validation errors={len(errors)} warnings={len(warnings)}")
    return 1 if errors else 0


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("command", choices=("normalize", "fix-links", "fix-php-links", "refactor-wrappers", "refactor-category-pages", "archive-pages", "build-indexes", "build-manifest", "migrate-page", "audit-media", "validate"))
    parser.add_argument("--apply", action="store_true", help="Apply changes; mutation commands otherwise run as a dry-run")
    parser.add_argument("--source", help="Legacy HTML source for migrate-page")
    parser.add_argument("--category", help="Destination category for migrate-page")
    args = parser.parse_args()
    commands = {
        "normalize": normalize_data,
        "fix-links": fix_links,
        "fix-php-links": fix_php_links,
        "refactor-wrappers": refactor_wrappers,
        "refactor-category-pages": refactor_category_pages,
        "archive-pages": archive_pages,
        "build-indexes": build_indexes,
        "build-manifest": build_manifest,
    }
    if args.command == "validate":
        return validate()
    if args.command == "audit-media":
        return audit_media()
    if args.command == "migrate-page":
        return migrate_page(args.source, args.category, args.apply)
    commands[args.command](args.apply)
    return 0


if __name__ == "__main__":
    sys.exit(main())
