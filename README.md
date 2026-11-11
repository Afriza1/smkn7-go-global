# SMKN 7 Jember Go Global

SMKN 7 Jember Go Global is a Laravel web platform for school information and services that support students, alumni, parents, teachers, and school administrators. This repository contains the application source, database migrations, Blade views, and the Vite-based frontend build configuration.

> This guide describes the repository as it exists today. Hosting providers differ, so replace example paths and PHP-FPM settings with values supplied by the server administrator.

## Project Overview

The application is a server-rendered Laravel platform. Laravel routes dispatch requests to controllers and Blade views; Eloquent models use the configured database connection. Tailwind CSS and JavaScript assets are compiled by Vite and loaded through Laravel's Vite integration.

The repository is configured for SQLite by default in `.env.example` for local setup. The database configuration also defines MySQL and other Laravel drivers. For a production MySQL deployment, configure the MySQL connection explicitly.

## Main Features

The routes, controllers, models, and views in this repository implement:

- Public school landing page and Japan career information and registration.
- Role-based dashboards and workflows for students, alumni, teachers, school administrators, the principal, and parents.
- Digital counseling, financial simulation, alumni information, social assistance, and parent/student verification workflows.
- Tracer Study submission, review, reporting, and historical records.
- Educational modules and departure-document guidance.
- Teacher-managed announcements with draft/published status.
- Teacher-managed gallery albums with multiple photos and public album pages.

Feature access is enforced by the routes and middleware in `routes/web.php`; the exact role available to a user depends on the route. Do not treat hiding a menu item as access control.

## Technology Stack

- PHP `^8.4.1` and Laravel Framework `^12.0`.
- MySQL is the documented production target; SQLite is the example local default. The Laravel database configuration also defines MariaDB, PostgreSQL, and SQL Server connections.
- Laravel Fortify, Livewire, and Spatie Laravel Permission are direct Composer dependencies.
- Tailwind CSS 4, Vite 6, and the Laravel Vite plugin.
- Composer and npm lockfiles are committed to keep dependency resolution reproducible.

## System Requirements

For a local development machine or production server, provide:

- PHP 8.4.1 or later in the PHP-FPM/web server runtime and command-line runtime. Both the Composer requirement and the locked dependency set require at least PHP 8.4.1; the lockfile also contains development dependencies with this minimum.
- Composer 2 with support for the Composer runtime API required by the lockfile.
- A database server and the matching PHP PDO driver. MySQL deployments need `pdo_mysql`; local SQLite setups need `pdo_sqlite`.
- Node.js supported by the locked Vite version: `^18`, `^20`, or `>=22`, plus npm. Node.js is required only where assets are built; a server receiving prebuilt `public/build` files does not need Node.js or `node_modules` to serve the application.
- A web server capable of routing requests to `public/index.php` and executing PHP. Apache with rewrite support, LiteSpeed, and Nginx/PHP-FPM can be configured for this purpose.
- HTTPS and a writable application storage area for sessions, logs, cache, and uploaded files.

The PHP version and extensions used by PHP-FPM must match the CLI environment used for Composer and Artisan commands.

## PHP Extensions

The locked Composer dependencies report these PHP platform requirements:

`ctype`, `date`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `phar`, `session`, `tokenizer`, `xml`, and `xmlwriter`.

Composer may satisfy `ctype` and `mbstring` through Symfony polyfills where native extensions are unavailable. Native extensions are recommended. In addition, enable the PDO driver for the selected database (`pdo_mysql` for MySQL or `pdo_sqlite` for SQLite).

Check the actual server against the lockfile with:

```bash
composer check-platform-reqs --lock
```

## Local Installation

The following steps create a **new local development installation**. They do not describe how to attach the application to an existing production database.

```bash
git clone https://github.com/Afriza1/smkn7-go-global.git
cd smkn7-go-global
```

Create a local environment file. On Linux/macOS:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Install dependencies and build frontend assets:

```bash
composer install
npm ci
npm run build
```

For a brand-new local installation only, create an application key:

```bash
php artisan key:generate
```

The example environment selects SQLite. Create its empty database file if it does not exist.

Linux/macOS:

```bash
touch database/database.sqlite
```

Windows PowerShell:

```powershell
if (-not (Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }
```

Then run the schema migrations against this empty local database:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

Open the URL printed by `artisan serve`. No seeder is required to create the schema. Inspect each seeder and its data before running any seeding command; do not assume development/demo seed data is appropriate for a real school installation.

## Environment Configuration

`.env.example` is a development template, not a production configuration. Its current defaults include local environment mode, debug enabled, a localhost URL, SQLite, database-backed sessions/cache/queue, local default filesystem storage, and log-based mail. Set production values in a private `.env` file or the hosting provider's secret/environment-variable facility.

At minimum, review these settings:

| Setting | Production guidance |
| --- | --- |
| `APP_ENV` | Set to `production`. |
| `APP_DEBUG` | Set to `false`; never expose stack traces to visitors. |
| `APP_URL` | Use the canonical HTTPS URL. |
| `APP_KEY` | Generate once for a genuinely new installation. Preserve the key that encrypted an existing database; see [Security Considerations](#security-considerations). |
| `DB_CONNECTION` | Set to `mysql` when using the project's MySQL production database. |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Set values supplied by the database administrator. Use a dedicated account with only required privileges. |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | The example uses database-backed drivers; ensure their backing tables and any required worker process are available, or deliberately configure supported alternatives. |
| `FILESYSTEM_DISK` | The example default is `local`. Announcement and gallery image uploads explicitly use the `public` disk. |
| `MAIL_MAILER` and `MAIL_*` | The example uses the `log` mailer. Configure a real, authenticated mail transport if the deployed workflows need outbound email. |

Do not copy `.env` from a developer machine into Git or a public directory. Do not place real passwords, tokens, or `APP_KEY` values in this README, issue reports, deployment archives, or shell history.

## Database Setup

### New local database

For the empty SQLite database created above, `php artisan migrate` creates the schema. For a local MySQL database, provision an empty database and a dedicated user, set the connection variables in `.env`, then run the migration command only against that disposable/local database.

### Existing or production database

**Do not run `php artisan migrate` against a production database as a routine deployment step.** This repository contains 30 migrations covering schema creation and later schema/data changes. In particular:

- `2026_11_10_090000_consolidate_tracer_study_into_tracer_studies.php` transforms the existing Tracer Study schema, copies and validates encrypted records, removes/rebuilds selected historical rows, and changes constraints/columns.
- `2026_11_10_092000_drop_tracer_study_support_tables.php` removes legacy Tracer Study support tables after its checks.
- The three announcement/gallery migrations create `announcements`, `gallery_albums`, and `gallery_photos`; the gallery photo foreign key cascades when an album is deleted. Their rollback methods drop the created tables.

The three feature migrations do not alter Tracer Study tables, but they still require a schema review and conflict check if similarly named tables already exist. No general safety guarantee can be made for the full migration set on an arbitrary populated database.

Before any production schema change, the database owner should:

1. Take and verify a restorable database backup outside the public web root.
2. Review the target database's migration history and schema without changing it.
3. Read the `up()` method of each migration that is actually pending and confirm its expected starting schema and data effects.
4. Approve a separate, controlled migration plan and maintenance window. Do not run all repository migrations blindly and do not run seeders without reviewing their effects.

This README intentionally provides no production `migrate` or seeding command.

## Frontend Asset Build

`package.json` defines `npm run build` using Vite. Build in the project checkout or a CI/build machine with the committed npm lockfile:

```bash
npm ci
npm run build
```

The build writes `public/build/manifest.json` and hashed assets under `public/build/assets/`. Deploy the complete `public/build/` directory alongside the matching application source. Do not deploy `public/hot` from a development Vite server. `node_modules/` is a build dependency and should not be part of the production package.

If a page reports that the Vite manifest is missing, confirm that `public/build/manifest.json` exists in the deployed release and that it was built from the same source revision.

## Production Deployment

This is a general checklist, not permission to modify an existing production database. Deploy a reviewed release from a commit or tag; do not package an arbitrary developer working tree.

1. Provision the server, HTTPS hostname, database, private application directory, and a web root that points to the Laravel `public/` directory.
2. Clone the approved repository release into the private application directory. Keep `.env`, database files/backups, and private storage outside the public web root.
3. Create the production environment configuration privately and verify the database and encryption-key guidance below before the application is allowed to read or write data.
4. Install PHP dependencies on the server, or deploy a reviewed production dependency package:

   ```bash
   composer install --no-dev --optimize-autoloader --no-interaction
   ```

5. Build assets in a build environment with Node/npm and deploy the complete `public/build/` directory. If assets are already included in a release package, confirm the manifest and referenced files instead of rebuilding from unrelated local files.
6. Take a database backup and follow the controlled database review in [Database Setup](#database-setup). Do not run production migrations or seeders as an automatic part of this generic procedure.
7. Configure the web server, storage link, and write permissions described below.
8. After production environment values are final, clear stale cached configuration if necessary, then cache Laravel's production configuration/routes/views:

   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

   Run Artisan commands from the private project directory with the same PHP runtime and environment as the deployed application. Do not run `key:generate` on an existing installation.
9. Verify the public site, login, role-protected dashboards, image upload/display, logs, HTTPS, and the database connection. Keep the backup until the release is accepted.

The repository's `.env.example` uses `MAIL_MAILER=log`; email delivery will not work until a real mail service is configured. The default database queue also requires an operational queue worker if the application dispatches queued work.

## Web Server Configuration

### Preferred: point the document root to `public/`

Set the domain's document root to the absolute path of the release's `public/` directory, for example `/srv/www/webgo/public`. The project root must remain outside the web root. This is the preferred setup for cPanel, LiteSpeed, Apache, and Nginx.

The committed `public/.htaccess` contains Apache rewrite rules. Apache/LiteSpeed must allow the required rewrite rules and route non-file requests to `public/index.php`. For Nginx, configure the site's root as Laravel's `public/` directory and use a `try_files` rule that forwards unresolved routes to `/index.php` through PHP-FPM. Use the PHP-FPM socket or host/port supplied by the administrator, and deny access to hidden files and dotfiles.

### cPanel alternative: separate `public_html`

If the provider cannot set the document root, keep the full Laravel project outside `public_html`, for example:

```text
/home/ACCOUNT/webgo/       # application, vendor, storage, bootstrap
/home/ACCOUNT/public_html/ # contents copied from webgo/public
```

Copy only the contents of Laravel's `public/` directory into `public_html`. Edit `public_html/index.php` so the maintenance file, Composer autoloader, and bootstrap paths point to the private project directory. For the example layout above, replace the default `__DIR__.'/../...'` paths with paths based on:

```php
$basePath = '/home/ACCOUNT/webgo';
```

Use `$basePath.'/storage/framework/maintenance.php'`, `$basePath.'/vendor/autoload.php'`, and `$basePath.'/bootstrap/app.php'` for the corresponding includes. Replace `ACCOUNT` with the real account name. Do not move `vendor`, `.env`, `storage`, or the rest of the project into `public_html`.

When `public_html` is separate, ensure its `/storage` URL maps only to `webgo/storage/app/public` using a supported symlink or hosting configuration. The default `php artisan storage:link` creates `webgo/public/storage`, which may not be the same directory as `public_html/storage`.

## Storage and Permission

Laravel needs the PHP process to write to `storage/` and `bootstrap/cache/`. Set ownership or ACLs for the PHP-FPM/web-server account. A typical Linux deployment uses owner/group permissions such as `775` for directories and `664` for files where appropriate; adapt this to the host's user/group model. Do not use world-writable `777` permissions as a shortcut.

Create the public storage link when the document root is the Laravel `public/` directory:

```bash
php artisan storage:link
```

The configured public disk stores files under `storage/app/public` and serves them through `public/storage`. Announcement cover images and gallery photos are public content. Do not put confidential documents or backups on this disk. The default `local` disk points to `storage/app/private`; keep that directory inaccessible from the web.

## Security Considerations

- Keep `.env` outside Git and inaccessible from the web. `.env.example` is a template and contains development defaults, not production-ready values.
- Set `APP_DEBUG=false`, use HTTPS, and set `APP_URL` to the canonical HTTPS origin.
- **Do not replace `APP_KEY` for an installation whose database already contains Laravel-encrypted values.** Tracer Study payloads/names and parent-portal student identities use Laravel encryption. A new key cannot decrypt old ciphertext. Keep the key associated with the existing data; if a verified key rotation is planned, configure the old key as a previous key and follow a separately tested re-encryption plan. Previous keys enable decryption but do not automatically rewrite stored ciphertext.
- Generate `APP_KEY` once only for a genuinely new installation with no encrypted data that must be retained. Store it in the server's private environment configuration, never in Git or the deployment ZIP.
- Use a dedicated database account with only the permissions required by the approved application and migration plan. Keep database backups outside the document root and restrict access to them.
- Review seeders before execution. Never seed production accounts or demo data without explicit verification.
- Keep Laravel logs and private uploads outside public access; apply server updates and restrict administrative access.

## Post-Deployment Checklist

- [ ] The web root points to Laravel's `public/` directory (or the isolated `public_html` alternative is configured correctly).
- [ ] PHP-FPM and CLI use compatible PHP versions and required extensions.
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` is HTTPS, and the existing installation's `APP_KEY` has not been replaced.
- [ ] Database settings point to the intended database; the application can connect without exposing credentials.
- [ ] Database backup is available and migration changes, if any, were separately reviewed and approved.
- [ ] `vendor/autoload.php` and `public/build/manifest.json` exist in the release.
- [ ] `storage/` and `bootstrap/cache/` are writable by the PHP process; private storage is not web-accessible.
- [ ] Public storage URLs work and confidential files are not stored on the public disk.
- [ ] Login, role-based route access, announcements, gallery pages, and image display work over HTTPS.
- [ ] Mail delivery and queue processing are configured if required by enabled workflows.
- [ ] Error pages do not disclose stack traces; logs are monitored and backups remain protected.

## Troubleshooting

| Symptom | Checks |
| --- | --- |
| HTTP 500 or blank page | Check `storage/logs/laravel.log`, PHP-FPM logs, PHP version/extensions, and write access to `storage/` and `bootstrap/cache/`. Keep `APP_DEBUG=false` on a public server. |
| `Vite manifest not found` | Deploy `public/build/manifest.json` and all referenced hashed assets from the same source revision. |
| Database connection error | Verify `DB_CONNECTION`, host, port, database name, the private credentials, and the matching PDO extension. |
| Migration reports an existing table or unexpected schema | Stop. Do not retry blindly. Compare the migration history and schema with the migration's expected starting point; take a verified backup and get database-owner review. |
| Encrypted value cannot be decrypted or appears empty | Confirm that the deployed `APP_KEY` is the key associated with that database and that the configured cipher/previous keys are correct. Do not generate a replacement key as a troubleshooting step. |
| Login redirects or CSRF/session errors repeat | Check HTTPS/proxy configuration, `APP_URL`, session storage availability, writable storage, and that all application instances use the same session and encryption configuration. |
| Uploaded image returns 404 | Confirm the public disk path, file permissions, the `public/storage` link (or separate `public_html/storage` mapping), and that the uploaded file is intended to be public. |
| Email is not sent | The example mailer writes to logs. Configure a valid production mail transport and sender address, then inspect mail logs/provider delivery. |
