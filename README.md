# AstroGallery

AstroGallery is an upload-ready PHP 8.1 modernization and preservation of the
Misti Mountain Observatory astronomy archive. It serves processed images,
observing details, historical articles, videos, and raw FITS exposures without
a database or server-side build.

## Local development

Requirements: PHP 8.1+, Python 3.10+ for content tools, Git, and Git LFS.

```text
git lfs install
git lfs pull
composer start
```

Open `http://localhost:8000`. If Composer is unavailable, run
`php -S localhost:8000 -t .` directly.

For subdirectory hosting, set `ASTRO_BASE_PATH=/astronomy` in the server
environment or edit `config/site.php` before upload.

## Content workflow

Object JSON under `image_card/card_data/` is authoritative. Each record contains
its stable ID, gallery memberships, canonical URL, image metadata, observing
details, and optional FITS downloads. Existing `image_card/*.php` files are thin
compatibility entry points.

After editing content:

```text
python tools/catalog.py build-indexes --apply
python tools/catalog.py build-manifest --apply
python tools/catalog.py validate
```

Mutation commands run as a dry-run unless `--apply` is supplied. Original HTML
is retained under `legacy/`; public `.htm` URLs are routed through the shared
layout.

## Resource layout

- `Images/`: processed images, thumbnails, and videos
- `fits/`: raw FITS exposures
- `process/`: processing tutorial images
- `gallery/`: generated indexes and resource manifest
- `legacy/`: protected original HTML archive
- `storage/`: protected runtime data and logs
- `assets/vendor/`: pinned third-party browser libraries and their licenses

FITS and MOV files use Git LFS but are ordinary complete files in a hydrated
working checkout. The application never contacts Git LFS at runtime.

Aladin Lite 3.8.1 is stored in `assets/vendor/aladin-lite/` so the application
does not depend on a version-changing JavaScript CDN. Survey tiles are loaded
from an official CDS HiPS mirror when an interactive sky map is opened.
OpenSeadragon 6.1.0 is stored in `assets/vendor/openseadragon/` and powers the
touch-friendly full-resolution image viewer.

## Verification

Run `composer test`, or run the two checks separately:

```text
python tools/catalog.py validate
php tests/smoke.php
```

See `DEPLOYMENT.md` for the FTP workflow and `CONTENT_LICENSE.md` before public
redistribution.
