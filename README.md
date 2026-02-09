# Mautic Multi Domain Plugin

Tracking domain rewriting for Mautic. Maps sender email addresses to tracking domains so emails and tracking JS use per-sender CNAME domains instead of the main Mautic URL.

## Features

- Replaces tracking domains in emails based on sender email address
- Rewrites list unsubscribe, image pixel, webview and unsubscribe tokens
- Rewrites all tracking JS domains to match the HTTP request domain (CNAME-aware)
- REST API for managing domain mappings (`/api/multidomain`)
- Role-based permissions for domain management
- Owner-as-mailer support (uses contact owner's email for domain lookup)
- RFC-compliant Message-ID headers per domain

## What does it do and why you need it

https://www.youtube.com/watch?v=O8_pcHMXV-M

## Requirements

- Mautic 5.x, 6.x or 7.x
- PHP 8.0+

## Installation

### Via Composer (Docker)

Ensure the composer directories exist with correct permissions:

```bash
docker exec --user root mautic_web mkdir -p /var/www/.composer/cache
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.composer
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm
```

Allow dev packages (only needed once per Mautic installation):

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config minimum-stability dev
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config prefer-stable true
```

Add the GitHub repository and install the plugin:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config repositories.mautic-multi-domain vcs \
  https://github.com/radata/mautic-multi-domain --no-interaction
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer require radata/mautic-multi-domain:dev-main -W --no-interaction
```

Update to the latest version:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer update radata/mautic-multi-domain -W --no-interaction
```

If the npm post-install hook fails after composer require, fix it:

```bash
docker exec --user root mautic_web rm -rf /var/www/html/node_modules
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm
docker exec --user www-data --workdir /var/www/html mautic_web npm ci --no-audit
```

### Manual Installation (docker cp)

```bash
docker cp plugins/MauticMultiDomainBundle mautic_web:/var/www/html/plugins/MauticMultiDomainBundle
docker exec --user root mautic_web chown -R www-data:www-data /var/www/html/plugins/MauticMultiDomainBundle
```

### Post-Installation

Clear cache (hard delete required), reload plugins, then enable in UI:

```bash
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console cache:warmup --env=prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console mautic:plugins:reload
```

1. Go to **Settings > Plugins > Multi Domain**
2. Set **Published** to **Yes**
3. Go to **Multi Domain** menu item > **New**: enter sender email + tracking domain
4. Ensure the tracking domain has a CNAME pointing to your Mautic URL

## API

REST API for managing domain mappings:

- **Get domain**: `GET /api/multidomain/ID`
- **List all**: `GET /api/multidomain`
- **Create**: `POST /api/multidomain/new` (body: `email`, `domain`)
- **Edit**: `PUT /api/multidomain/ID/edit` or `PATCH /api/multidomain/ID/edit` (body: `email`, `domain`)
- **Delete**: `DELETE /api/multidomain/ID/delete`

## Permissions

The plugin uses the Mautic permissions system. Roles can be configured for domain management access.

## Uninstall

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer remove radata/mautic-multi-domain -W --no-interaction
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config --unset repositories.mautic-multi-domain
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console cache:warmup --env=prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console mautic:plugins:reload
```

## Credits

Original work: https://github.com/friendly-ch/mautic-multi-domain
Upgrade by: https://github.com/rjocoleman

## License

MIT - see [LICENSE](LICENSE) for details.
