# FTP deployment

## Prepare the local checkout

1. Run `python tools/catalog.py validate`.
2. Run `php tests/smoke.php` with PHP 8.1 or newer.
3. Set the deployment base path in `config/site.php` or through
   `ASTRO_BASE_PATH` when the host supports environment variables.

## FileZilla synchronization

Upload from the repository working directory. Exclude `.git/`, `.cache/`,
`__pycache__/`, `tests/`, and `tools/`. Do not exclude `Images/`, `process/`,
`gallery/`, `image_card/`, `legacy/`, or `storage/.htaccess`.

The server does not need Git, Python, Node, Composer, or a build step.

After synchronization, smoke-test the home page, every gallery, a sky-map object,
a migrated `.htm` URL, and the image viewer. Preserve the prior server copy until
these checks pass.
