# Apache + PHP-FPM setup for METODIKA

This folder contains ready-to-adapt templates for CodeIgniter 4 in this repository.

## Files

- `metodika-dev.conf`: local development VirtualHost (HTTP)
- `metodika-prod.conf`: production/staging VirtualHost (HTTP -> HTTPS + TLS)
- `metodika-php-fpm-pool.conf`: dedicated PHP-FPM pool template

## Prerequisites (Ubuntu/Debian)

1. Install runtime packages:
   - `apache2`
   - `php8.4-fpm`
2. Enable modules:
   - `a2enmod rewrite headers ssl proxy proxy_fcgi setenvif`
3. Disable default site if needed:
   - `a2dissite 000-default.conf`

## Local development flow

1. Add hosts entry:
   - `127.0.0.1 metodika.local`
2. Copy and enable vhost:
   - `sudo cp codei/deploy/apache/metodika-dev.conf /etc/apache2/sites-available/`
   - `sudo a2ensite metodika-dev.conf`
3. Make sure PHP-FPM socket path in vhost exists.
   - default dev template uses `/run/php/php8.4-fpm.sock`
4. Restart services:
   - `sudo systemctl restart php8.4-fpm`
   - `sudo systemctl restart apache2`

## Production flow

1. Copy site templates and adjust domain/paths/TLS cert paths.
2. Optional but recommended: use dedicated PHP-FPM pool from `metodika-php-fpm-pool.conf`.
   - prod template expects `/run/php/php8.4-fpm-metodika.sock`
3. Enable site and reload:
   - `sudo a2ensite metodika-prod.conf`
   - `sudo systemctl reload php8.4-fpm`
   - `sudo systemctl reload apache2`
4. Verify config:
   - `sudo apache2ctl configtest`

## Notes for this repository

- DocumentRoot must always point to `codei/public`.
- Existing rewrite rules are in `codei/public/.htaccess`.
- Production should keep `CI_ENVIRONMENT=production`.
- Keep `setup.php` restricted and token-protected.
