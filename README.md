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

## Testing

```bash
composer test
```

## Deployment

Pushes to `main` trigger `.github/workflows/deploy.yml`, which installs dependencies, rsyncs the codebase to a Hostinger server over SSH, runs `php artisan migrate --force`, and caches config/routes/views.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
