# Project Overview & Maintenance Guide

Last scanned: 2025-12-16

## Project Overview

- Purpose: Backend for Lamavie — admin dashboard and public APIs to manage bookings, drivers, labs, users, and related services.
- Key features: Admin dashboard, booking lifecycle, driver and lab workflows, notifications (FCM), Excel exports, and multiple auth guards (admin, driver, lab, user).
- Technology stack:
  - Framework: Laravel 12 (composer requirement: `laravel/framework` ^12.0). See [composer.json](composer.json).
  - PHP: ^8.2 (composer). See [composer.json](composer.json).
  - Auth: session guards + Laravel Sanctum for API (package present in composer.json). See [config/auth.php](config/auth.php).
  - Database: Relational DB (migrations target typical MySQL/Postgres; tests use in-memory sqlite). See [database/migrations](database/migrations).
  - Queue/Workers: project is configured to use Laravel queue (scripts reference `php artisan queue:listen`).
  - Third-party packages: `kreait/laravel-firebase`, `maatwebsite/excel`, `laravel/sanctum`, `laravel/pint`, `phpunit/phpunit`.

## High-level Architecture & Data Flow

- HTTP traffic handled via `routes/web.php` for web & admin routes and `routes/api.php` for API endpoints.
- Authenticated admin users use `auth:admin` guard and Blade dashboard views under `resources/views/admin`.
- Controllers act as HTTP adapters; business logic should live in `app/Services/` or domain classes (convention recommended).
- Background tasks (if any) are dispatched to the queue and handled by worker processes.

## Directory Structure and Key Files

- `app/` — core app code
  - `app/Models/` — Eloquent models (Admin, Booking, Driver, Lab, Service, etc.). See [app/Models](app/Models).
  - `app/Http/Controllers/` — Controllers grouped by area (Dashboard, API, Driver, Lab).
  - `app/Http/Middleware/` — Custom middleware (e.g., admin logging).
  - `app/Exports/` — Excel export classes (used with Maatwebsite Excel).

- `routes/`
  - `routes/web.php` — main web routes (admin, driver, lab prefixes). See [routes/web.php](routes/web.php).
  - `routes/api.php` — API routes.

- `resources/views/` — Blade templates (admin dashboard, auth pages, partials). See [resources/views](resources/views).

- `database/`
  - `database/migrations/` — schema definitions (many migration files present). See [database/migrations](database/migrations).
  - `database/seeders/` — seeders (PermissionsSeeder added).

- `config/` — configuration files (auth.php, permission.php, system_logs.php, etc.). Review [config/auth.php](config/auth.php).

- Other files:
  - `composer.json` — dependencies and scripts. See [composer.json](composer.json).
    - Note: `ai/onboarding.md` created as the canonical AI onboarding file; the **AI Onboarding Snippet** in this guide is a quick reference.
  - `phpunit.xml` — test config (uses in-memory sqlite for tests). See [phpunit.xml](phpunit.xml).
  - `ai/ai.md` — repository onboarding prompt for AI agents.

## Database Schema and Models (summary)

I inspected migrations in `database/migrations` and models in `app/Models`. Below is a concise mapping of major tables and their intended relationships. This is a summary — consult individual migration files for exact columns.

- Users & Auth
  - `users` (`app/Models/User.php`): primary application users.
  - `admins` (`app/Models/Admin.php`): admin users; used with the `admin` auth guard. (Role/permission system is not implemented in this repository.)
  - `drivers` (`app/Models/Driver.php`), `labs` (`app/Models/Lab.php`): separate auth providers/guards.

- Booking domain
  - `bookings` (`app/Models/Booking.php`): central entity linking to `users`, optional `driver_id`, `lab_id`, and related assignments.
  - `booking_car_assignments` (`app/Models/BookingCarAssignment.php`): assignments of cars to bookings.

- Catalog / services
  - `services`, `service_types`, `service_categories`, `your_items`, `carpet_material`, `carpet_size`, `type_of_stain`, etc.: domain tables for pricing and selection.

- Admin & audit
  - `admins` table plus `system_logs` (`app/Models/SystemLog.php`) migration present.

- Other notable tables: `drivers`, `driver_vehicles`, `car_wash_drivers`, `areas`, `notifications`, `user_points`, `home_banners`.

Assumptions & notes:
- Exact column lists and constraints are defined in the individual migration files under `database/migrations` — consult those files for exact types and indices.
- Many migrations add `price` columns and `soft deletes` to `bookings`; adapt carefully when changing.

## Eloquent Models (summary)

- `app/Models` contains many models (Admin, Booking, Driver, Service, etc.). Key points:
  - `Admin` model: configured as the `admin` guard model; review for authorization needs.
  - Models appear to follow typical Eloquent conventions; ensure `casts()` and `fillable` are correctly set per model.
  - Add or centralize relationship methods (belongsTo, hasMany) where missing and prefer eager loading in controllers for heavy queries.

## Traits

The project defines application-specific Traits under `app/Traits/` and also uses package traits (e.g., Spatie's `HasRoles` on the `Admin` model).

- `App\Traits\ApiResponse`
  - Purpose: Provides `successResponse()` and `errorResponse()` helpers to standardize JSON API responses.
  - Usage: Widely used in API controllers under `app/Http/Controllers/Api/*` and in middleware (`EnsureUserIsAuthenticated`) to return consistent JSON payloads.
  - Recommendation: Keep the trait small (as-is). Prefer using it from a base API controller so controllers don't `use` the trait directly unless needed.

- `App\Traits\HandlesMediaUploads`
  - Purpose: Small helper methods to attach, update, and delete media on models (wraps typical usage of file uploads and media collections).
  - Usage: Present in the codebase but not widely referenced; intended for controllers or services handling file uploads (compatible with Spatie MediaLibrary-like patterns).
  - Recommendation: Move media upload orchestration into a `MediaService` under `app/Services/` and keep trait for low-level helpers. Add unit tests for edge cases (invalid files, arrays, deletion).

- Package traits:
  - `Spatie\Permission\Traits\HasRoles` — used on `app/Models/Admin.php` to enable roles/permissions functionality for the `admin` guard.
  - Recommendation: Keep guard consistency (`protected $guard_name = 'admin'`) and centralize permission checks (middleware + Blade directives).

Locate and usage notes:
- Traits folder: `app/Traits/` contains `ApiResponse.php` and `HandlesMediaUploads.php`.
- Search: `ApiResponse` is imported in many API controllers; `HandlesMediaUploads` is a low-level helper available to controllers/services.


## Routing and Controllers

- `routes/web.php` defines main routes with prefixes:
  - Root redirects to admin login/dashboard depending on `Auth::guard('admin')`.
  - `driver` prefix: driver auth and driver-facing endpoints (middleware `auth:driver`).
  - `lab` prefix: lab auth and endpoints (middleware `auth:lab`).
  - `admin` prefix: admin dashboard & resources, protected with `auth:admin` and a logging middleware (`App\\Http\\Middleware\\LogAdminActivity`).

- Controller responsibilities (convention & recommendations):
  - Keep controllers small: validate input, orchestrate Services, return responses/views.
  - Move business logic to `app/Services/` for testability.
  - For long resource controllers (e.g., `BookingController`), break into smaller actions or use Form Requests for validation.

## Views and Frontend Integration

- Blade templates live in `resources/views/` with subfolders: `admin`, `auth`, `dashboard`, `driver`, `lab`.
- Asset pipeline: uses Vite (`vite.config.js`) and npm scripts; `composer.json` 'dev' script references `npm run dev` and `php artisan pail`.

## Authentication and Authorization

- Guards configured: `admin`, `driver`, `lab`, `user` and default `sanctum` for API. See [config/auth.php](config/auth.php).
- Role/permission system: Not implemented. Route protection: `auth:admin`, `auth:driver`, `auth:lab` used widely. Use middleware and policies for authorization.

## Testing and Quality Assurance

- Test runner: PHPUnit configured in `phpunit.xml` and Composer `test` script runs `php artisan test`.
- Dev tools: `laravel/pint` present for code style.
- Tests: `tests/Unit` and `tests/Feature` exist — expand coverage for critical flows (auth, booking lifecycle, permission checks).

## Deployment and Environment Setup

Basic local setup (developer):

1. Install deps:
```bash
composer install
npm install
```
2. Copy env and generate key:
```bash
cp .env.example .env
php artisan key:generate
```
3. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\PermissionsSeeder
```
4. Run dev server & vite:
```bash
npm run dev
php artisan serve
```

Production checklist:
- Use proper DB (MySQL/Postgres) and configure `APP_ENV=production`, `APP_DEBUG=false`.
- Run `php artisan config:cache`, `route:cache`, `view:cache`.
- Run queue workers (`php artisan queue:work --sleep=3 --tries=3`).
- Secure environment variables and file permissions.

CI/CD notes:
- Composer scripts include `post-create-project-cmd` migration command — prefer using explicit CI pipeline that runs `composer install --no-dev`, `php artisan migrate --force`, and tests.

## Maintenance Guidelines

- Adding features:
  - Add migrations incrementally and make them reversible.
  - Add seeders with idempotent `firstOrCreate` calls.
  - Place business logic into `app/Services/` and keep controllers thin.
  - Add tests for new behavior (feature tests for HTTP flows, unit tests for Services).

- Upgrading dependencies:
  - Run `composer outdated` and upgrade packages in a separate branch.
  - Run the test suite and scan for deprecations.
  - For Laravel upgrades, follow upgrade guide step-by-step and run `artisan` commands to refresh configs.

- Permissions & security:
  - Keep `Admin` roles/permissions seeders under `database/seeders` and review `guard_name` consistency.
  - Use `@can` in Blade and `permission:` middleware for route protection.

## Known pain points & recommendations

- Large controllers/resource routes: consider extracting Services and Form Requests to reduce complexity.
- Migrations: there are many incremental migrations; consider squashing in a new fresh schema for new deployments (only for new projects).
- Tests: increase coverage for booking workflows and permission checks.
- Documentation: `ai/ai.md` is present — expand with module-level READMEs and a contributors guide.

## Actionable Next Steps (priority)

1. Run `composer install` and `php artisan migrate` (in a dev environment) and seed permissions.
2. Add a short `app/Services/README.md` with a Service template and create one Service for booking operations as a reference.
3. Add feature tests covering admin permission checks and booking lifecycle.
4. Add CI pipeline steps: `composer install`, `php artisan test`, `php artisan migrate --force` (staging), asset build.

## Assumptions & Gaps

- I inspected key files in the repository but did not execute the application. Where exact column types, constraints, or runtime behavior matter, consult the migration files and run the app locally.
- This guide emphasizes maintainability and recommended structure rather than rewriting existing business logic.

---

For specific follow-ups I can: generate Service scaffolding, add a sample feature test for role/permission checks, or create CI pipeline templates (GitHub Actions or GitLab CI). Which should I do next?

## AI Onboarding Snippet (include with every prompt to AI agents)

When sending any prompt to an AI agent about this repository, include the following snippet so the agent has immediate context and the preferred prompt style. Paste the entire block below (English + Arabic guidance) before your question.

---

Repository context summary (send this first):

- Project: Lamavie backend — Laravel 12, PHP ^8.2. Backend for admin dashboard and APIs managing bookings, drivers, labs, users, and services.
- Key folders: `app/` (models, controllers, middleware), `routes/`, `resources/views/`, `database/migrations`, `database/seeders`, `config/`.
- Guards: `admin`, `driver`, `lab`, `user`. Route protection uses `auth:admin`, `auth:driver`, `auth:lab`.

AI instructions (always include):

1. Read and understand `project_overview_and_maintenance_guide.md` first.
2. When proposing code changes: produce a short plan, then output focused patches. Prefer small, reversible changes and add tests when feasible.
3. Keep controllers thin; prefer `app/Services/` for business logic. Use Form Requests for validation.

Example short onboarding prompt to include literally:

"aba 3ayez el file dh yb2aa feh prompt keda ab3ato daymna w ana batlob ay haba mn el ai agent 3shan yb2aa fahem el project w howa shaghalw kaman ekteb en dh laravel 12 mafesh kernal w zabata keda el prompt dh\n\nاكتب: ده Laravel 12 — مافيش Kernel و زبطها كده."

---

Include this section at the top of every prompt you send to any AI agent so it always has project context and the expected response style.
