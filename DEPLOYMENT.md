# FTP deployment

## Prepare the local checkout

1. Install Git LFS and run `git lfs pull`.
2. Run `python tools/catalog.py validate`.
3. Run `php tests/smoke.php` with PHP 8.1 or newer.
4. Confirm FITS and MOV files are full-sized files, not LFS pointer text.
5. Set the deployment base path in `config/site.php` or through
   `ASTRO_BASE_PATH` when the host supports environment variables.

## FileZilla synchronization

Upload from the repository working directory. Exclude `.git/`, `.cache/`,
`__pycache__/`, `tests/`, and `tools/`. Do not exclude `Images/`, `fits/`,
`process/`, `gallery/`, `image_card/`, `legacy/`, or `storage/.htaccess`.

The server does not need Git, Git LFS, Python, Node, Composer, or a build step.
Do not upload from an unhydrated archive produced by a Git web interface because
such archives can contain LFS pointers instead of the media payloads.

After synchronization, smoke-test the home page, every gallery, a sky-map object,
a FITS download, a migrated `.htm` URL, and the image viewer. Preserve the prior
server copy until these checks pass.
