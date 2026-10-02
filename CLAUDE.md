# personal-website-be

Stateless Laravel 13 (PHP 8.3) JSON API powering irfanmim's personal portfolio site.
Pairs with the sibling repo `../personal-website-fe` (Vue 3 SPA) — see the workspace
root CLAUDE.md for how the two fit together.

## Setup
- `composer install`
- `cp .env.example .env && php artisan key:generate`
- `php artisan migrate` (or `--force` for non-interactive use)
- `php artisan db:seed` — idempotent (safe to re-run), seeds admin user from
  `ADMIN_USERNAME`/`ADMIN_PASSWORD` env vars plus Hero/About/Contact/Project/Experience data
- Or run all of the above in one shot: `composer run setup`

## Running locally
- `composer run dev` — runs `php artisan serve` + queue listener + `pail` + Vite concurrently
- Or Docker: `docker compose up -d --build` (services: `app` PHP-FPM, `web` nginx on
  :8000, `db` MySQL). A fourth `frontend` service will also start if
  `../personal-website-fe` exists as a sibling checkout (or `$FRONTEND_PATH` points to
  it), serving Vite on :5173 with `VITE_API_URL=http://localhost:8000` auto-set.
- After bringing up Docker: `docker compose exec app php artisan migrate --force`

## Tests
- `composer test` (= `php artisan config:clear && php artisan test`), PHPUnit, suites
  in `tests/Unit` and `tests/Feature`.
- Besides the stock example tests, `tests/Feature/SkillsTest.php` covers `PUT /content/skills`
  (auth, validation, round-trip); no other controllers/endpoints have coverage yet.

## Lint/format
- `vendor/bin/pint` (Laravel Pint, default preset — no `.pint.json` committed).
  `vendor/bin/pint --test` for a dry-run/check-only pass. Not wired into CI.

## API shape (`routes/api.php`, all under `/api`)
- Public: `POST /auth/login`, `GET /content` (hero+about+contact+experiences),
  `GET /projects`, `GET /experiences`
- Protected (`auth:sanctum`, bearer token from `/auth/login`):
  - `POST /auth/logout`, `GET /auth/me`
  - `PUT /admin/username`, `PUT /admin/password`
  - `PUT /content/hero|about|contact`
  - `PUT /content/skills` — replaces the whole hero-chart skill list (`skills[]` with
    `key, label, shortLabel, pillar, level 1–10, tech[], visible`; array order = display order).
    `GET /content` returns the same list under `skills`; the seeder only fills it on a fresh
    install, and deploys run `migrate` only, so the frontend falls back to its defaults when empty.
  - Projects: `PUT /projects/reorder`, `POST /projects`, `PUT|POST /projects/{id}`,
    `DELETE /projects/{id}`
  - Experiences: `PUT /experiences/reorder`, `POST /experiences`,
    `PUT|DELETE /experiences/{id}`, plus nested company sub-routes
    (`POST /experiences/{id}/companies`, `PUT|DELETE /experiences/{roleId}/companies/{companyId}`)
- **Gotcha:** `/reorder` routes are declared before `/{id}` routes to avoid Laravel
  route-matching conflicts — keep that ordering when adding routes.
- Auth is stateless bearer-token (Sanctum), not cookie sessions:
  `SESSION_DRIVER=array`, `SANCTUM_STATEFUL_DOMAINS` intentionally left empty.

## Deployment
- Push to `main` → `.github/workflows/deploy.yml` → rsync+SSH to a Hostinger server,
  runs `php artisan migrate --force` and `optimize:clear`/`optimize` remotely.
  Excludes `.git/`, `.github/`, `node_modules/`, `tests/`, `.env` from the rsync.
