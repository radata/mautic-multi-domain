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

- Mautic 7.x (Docker FPM image)
- PHP 8.0+

## Installation

### Via Composer (Docker)

Ensure the composer and npm directories exist with correct permissions:

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
  composer require radata/mautic-multi-domain:dev-main \
  -W --no-interaction --ignore-platform-req=ext-gd
```

> The `--ignore-platform-req=ext-gd` flag is needed because the `mautic/mautic:7.0-fpm` Docker image has a broken GD CLI extension (`libavif.so.15` missing). GD works fine at runtime via PHP-FPM.

Update to the latest version:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer update radata/mautic-multi-domain \
  -W --no-interaction --ignore-platform-req=ext-gd
```

If the npm post-install hook fails after composer require, fix it:

```bash
docker exec --user root mautic_web rm -rf /var/www/html/node_modules
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm
docker exec --user www-data --workdir /var/www/html mautic_web npm ci --no-audit
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

## Plugin Structure

```
plugins/MauticMultiDomainBundle/
├── Assets/img/
│   └── icon.png                             # Plugin icon
├── Config/config.php                        # Service, route & menu registration
├── Controller/
│   ├── Api/
│   │   └── MultidomainApiController.php     # REST API controller
│   └── MultidomainController.php            # UI controller (list, create, edit, delete)
├── Entity/
│   ├── Multidomain.php                      # Domain mapping entity (email + domain)
│   └── MultidomainRepository.php            # Database queries
├── Event/
│   └── MultidomainEvent.php                 # Custom event class
├── EventListener/
│   ├── BuilderSubscriber.php                # Rewrites tracking URLs in emails
│   ├── BuildJsSubscriber.php                # Rewrites tracking JS domains
│   └── MultidomianSubscriber.php            # Audit log & domain event handling
├── Form/Type/
│   └── MultidomainType.php                  # Domain mapping form
├── Model/
│   └── MultidomainModel.php                 # Business logic & domain lookups
├── Resources/views/Multidomain/
│   ├── details.html.twig                    # Detail view
│   ├── form.html.twig                       # Create/edit form
│   ├── index.html.twig                      # Index page
│   └── list.html.twig                       # List table
├── Security/Permissions/
│   └── MultidomainPermissions.php           # Role-based access control
├── Translations/
│   └── en_US/
│       ├── messages.ini
│       └── validators.ini
├── MauticMultiDomainBundle.php              # Bundle class
└── composer.json
```

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
