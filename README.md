# Personal Website API

A stateless Laravel JSON API that powers the backend of a personal portfolio website: public content endpoints for the site itself, and Sanctum-protected admin endpoints for managing that content.

## Overview

The site's content (hero, about, contact, projects, experience) lives in the database and is fully editable through an authenticated admin API — no code changes or redeploys needed to update copy or projects. A single admin account (backed by `ADMIN_USERNAME` / `ADMIN_PASSWORD`) logs in to receive a Sanctum bearer token used for all write operations.

## Tech stack

- PHP 8.3 / Laravel 13
- Laravel Sanctum for stateless bearer-token authentication
- MySQL
- Vite + Tailwind (asset bundling only — this app has no frontend UI)

## Domain model

| Resource | Notes |
|---|---|
| `Hero` | Singleton — name, role |
| `About` | Singleton — bio |
| `Contact` | Singleton — LinkedIn, GitHub, Instagram, CV URL |
| `Project` | Ordered list — title, description, tags, demo link, image, `show_demo_soon` flag |
| `ExperienceRole` | Ordered list of roles, each with one or more `ExperienceCompany` entries |
| `User` | Single admin user (username + hashed password), issues Sanctum tokens |

## API routes

All routes are prefixed with `/api`.

**Public**

```
POST /auth/login          Authenticate admin, returns a Sanctum bearer token
GET  /content              Aggregate hero + about + contact + experiences
GET  /projects              List projects
GET  /experiences           List experience roles with their companies
```

**Protected** (`Authorization: Bearer <token>`)

```
POST   /auth/logout
GET    /auth/me

PUT    /admin/username
PUT    /admin/password

PUT    /content/hero
PUT    /content/about
PUT    /content/contact

PUT    /projects/reorder
POST   /projects
PUT|POST /projects/{id}
DELETE /projects/{id}

PUT    /experiences/reorder
POST   /experiences
PUT    /experiences/{id}
DELETE /experiences/{id}
POST   /experiences/{id}/companies
PUT    /experiences/{roleId}/companies/{companyId}
DELETE /experiences/{roleId}/companies/{companyId}
```

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env`:

- `DB_*` — MySQL connection
- `ADMIN_USERNAME` / `ADMIN_PASSWORD` — admin login credentials
- `FRONTEND_URL` — origin allowed by CORS
- `SANCTUM_STATEFUL_DOMAINS` — leave empty; tokens are stateless bearer tokens, not cookie sessions

Then run migrations and start the app:

```bash
php artisan migrate
php artisan serve
```

Or use the all-in-one dev script (server + queue listener + log tailing + Vite):

```bash
composer run dev
```

## Docker

Runs the API (PHP-FPM + nginx), a MySQL database, and — if you also have the [frontend repo](../personal-website-fe) checked out as a sibling directory — its Vite dev server, all with one command.

```bash
# clone the frontend as a sibling directory first, e.g.:
#   ~/code/personal-website-be   (this repo)
#   ~/code/personal-website-fe
# or point at a different location via FRONTEND_PATH in .env

cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed   # optional — safe to re-run
```

- API: `http://localhost:8000`
- Frontend dev server: `http://localhost:5173` (only starts successfully if the sibling `personal-website-fe` checkout — or `FRONTEND_PATH` — exists)
- `docker compose exec app php artisan tinker` — REPL inside the app container
- `docker compose logs -f app|web|frontend` — tail logs for a service
- `docker compose down` — stop everything; add `-v` to also delete the MySQL data, installed Composer `vendor/`, and frontend `node_modules` volumes

`APP_KEY` is generated automatically on first boot (into your bind-mounted `.env`) if it's empty. `DB_HOST`/`DB_PORT` in `.env` are for bare-metal use only — Compose overrides them to point at the `db` container.

This setup is independent of `.github/workflows/deploy.yml` (Hostinger) and the frontend's own static-site deploy — neither is affected by Docker.

## Testing

```bash
composer test
```

## Deployment

Pushes to `main` trigger `.github/workflows/deploy.yml`, which installs dependencies, rsyncs the codebase to a Hostinger server over SSH, runs `php artisan migrate --force`, and caches config/routes/views.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
