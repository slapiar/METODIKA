# Hostinger Business (shared hosting) setup for METODIKA

This guide is for Hostinger shared hosting where webserver and PHP-FPM are managed by provider.

## Important context

- This project is CodeIgniter 4.
- Public web root must expose only `codei/public`.
- PHP requirement is 8.2+ (project currently matches this requirement).

## Variant A (recommended): Custom document root to `codei/public`

Use this when hPanel allows changing the website root.

1. Upload project files so folder `codei/` exists on hosting.
2. In Hostinger hPanel, set website root to `.../codei/public`.
3. Keep `.htaccess` from `codei/public/.htaccess` as-is.
4. In `codei/.env` set:
   - `CI_ENVIRONMENT = production`
   - `app.baseURL = 'https://your-domain.tld/'`
5. Ensure writable permissions for `codei/writable`.
6. Open website and verify routes without `index.php` in URL.

## Variant B: If document root cannot be changed (fallback)

Use this only when hPanel forces `public_html` as web root.

### B1. Redirect via `public_html/index.php` (your preferred option)

1. Keep project folder as `public_html/codei/...`.
2. Create `public_html/index.php` from template `public_html-index.php`.
3. Optionally create `public_html/.htaccess` from template `public_html.htaccess`.
4. In `codei/.env` set:
  - `CI_ENVIRONMENT = production`
  - `app.baseURL = 'https://your-domain.tld/codei/public/'`

Note: This keeps the URL path with `/codei/public/...` and is less clean than custom root.

### B1b. Subdomain points directly to `codei/` (your current approach)

1. Point subdomain to the `codei/` directory.
2. Use root shim files:
  - `codei/index.php`
  - `codei/.htaccess`
3. The shim redirects requests to `/public/...` using HTTP 307.
4. In `codei/.env` set:
  - `CI_ENVIRONMENT = production`
  - `app.baseURL = 'https://your-subdomain.tld/public/'`

This approach is compatible with multi-subdomain setups under one `public_html`.

### B1c. Domain path setup `https://domain.tld/codei/` (your current value)

1. Keep website root unchanged (`public_html`) and deploy app under `public_html/codei`.
2. Keep shim files in `codei/`:
  - `codei/index.php`
  - `codei/.htaccess`
3. Set base URL to the `/codei/` path and remove `index.php` from generated URLs:
  - `app.baseURL = 'https://domain.tld/codei/'`
  - `app.indexPage = ''`
4. Open test URLs:
  - `https://domain.tld/codei/`
  - `https://domain.tld/codei/some-route`

Expected behavior:

- `/codei/` is a valid public URL
- `/codei/some-route` is a valid public URL
- application routes resolve in CodeIgniter
- no 404 for valid routes

### B1d. Optional no-redirect variant

Use this only if you want cleaner runtime behavior without HTTP redirect on each request.

1. Replace `codei/index.php` with template `codei-index-internal-bootstrap.php`.
2. Replace `codei/.htaccess` with template `codei-htaccess-no-redirect.template`.
3. Set base URL to:
  - `app.baseURL = 'https://domain.tld/codei/'`
4. Test dynamic route and static asset URL.

### B2. Cleaner fallback without root change

1. Copy contents of `codei/public/` into `public_html/`.
2. Keep `codei/` outside `public_html` if your account layout allows it.
3. Edit `public_html/index.php` paths to point to real `app` and `system` locations.
4. In `codei/.env` set:
  - `CI_ENVIRONMENT = production`
  - `app.baseURL = 'https://your-domain.tld/'`

Security note: Variant A is preferred because it avoids exposing non-public project files.

## Hostinger PHP settings

- Select PHP 8.2+ in hPanel (prefer newest stable available).
- Typical production defaults:
  - display_errors = Off
  - log_errors = On
  - memory_limit = 256M (or higher if needed)
  - max_execution_time = 120

A sample `.user.ini` is included in this folder.

Additional templates in this folder:

- `public_html-index.php`
- `public_html.htaccess`
- `codei-index-internal-bootstrap.php`
- `codei-htaccess-no-redirect.template`

## Quick checks after deploy

1. Homepage opens without 500 error.
2. Existing routes work without `index.php`.
3. `writable/` is writable by PHP.
4. Error pages do not leak stack traces in production.

## Relevant files in this repository

- `codei/public/.htaccess`
- `codei/public/index.php`
- `codei/app/Config/App.php`
- `codei/app/Config/Boot/production.php`
