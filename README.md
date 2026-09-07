# WTG Spain API

[![Tests](https://github.com/Zenich75/wtgtest/actions/workflows/tests.yml/badge.svg)](https://github.com/Zenich75/wtgtest/actions/workflows/tests.yml)
[![Pint](https://github.com/Zenich75/wtgtest/actions/workflows/pint.yml/badge.svg)](https://github.com/Zenich75/wtgtest/actions/workflows/pint.yml)
[![codecov](https://codecov.io/gh/Zenich75/wtgtest/branch/master/graph/badge.svg)](https://codecov.io/gh/Zenich75/wtgtest)

A Laravel 12 API for ingesting supplier hotel offers, searching property availability, and reserving offer units.

## Overview

The application exposes four endpoints:

- `POST /api/imports` — accepts a batch of supplier offers, creates an `Import` record idempotently, and dispatches a background job to upsert the corresponding `Property` and `Offer` records.
- `GET /api/imports/{import}` — returns the status of an import (`pending` / `processing` / `completed` / `failed`).
- `GET /api/properties` — searches properties with an available offer matching `city` (optional), `check_in`, `check_out`, and `guests`, returning the cheapest matching offer per property, paginated.
- `POST /api/offers/{offer}/reservations` — reserves one unit of an offer for a customer.

Domain models: `Supplier`, `Property`, `Offer`, `Import`, `Reservation`.

## Requirements

- Docker (this project uses [Laravel Sail](https://laravel.com/docs/sail) for local development)
- Composer (only needed if you want to run tooling outside of Sail)

## Setup

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Copy the environment file:

   ```bash
   cp .env.example .env
   ```

   `.env.example` defaults to `DB_CONNECTION=sqlite`. To use Sail's MySQL container instead, set:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=sail
   DB_PASSWORD=password
   ```

   `QUEUE_CONNECTION=database` is already the default — see [Queue Worker](#queue-worker) below.

3. Start the containers:

   ```bash
   ./vendor/bin/sail up -d
   ```

4. Generate the application key (inside the container):

   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

## Database

Run migrations:

```bash
./vendor/bin/sail artisan migrate
```

Seed reference and demo data — this creates the two suppliers (`supplier-a`, `supplier-b`) that imports validate against, plus demo properties, offers, imports, and reservations so the [data browser](#data-browser) has something to show:

```bash
./vendor/bin/sail artisan db:seed
```

Or do both at once, dropping all tables first:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

> There is no API endpoint to create suppliers — `POST /api/imports` validates that the `supplier` code already exists, so the seeder (or a manually inserted row) must run first.

## Queue Worker

Imports are processed asynchronously by `ProcessImportJob` (upserting `Property`/`Offer` records inside a database transaction). With `QUEUE_CONNECTION=database` (the default), a worker must be running for imports to progress past `pending`:

```bash
./vendor/bin/sail artisan queue:work
```

For local testing without a worker, set `QUEUE_CONNECTION=sync` in `.env` so jobs run inline during the request.

## Running Tests

Tests run against their own MySQL database (`laravel_api_test`), configured in `.env.testing` — never against the dev database (`laravel`). Create it once inside the MySQL container:

```bash
./vendor/bin/sail exec mysql mysql -uroot -p"${DB_PASSWORD}" -e "CREATE DATABASE IF NOT EXISTS laravel_api_test; GRANT ALL PRIVILEGES ON laravel_api_test.* TO '${DB_USERNAME}'@'%';"
```

`RefreshDatabase` migrates this database and wraps each test in a rolled-back transaction, so it stays empty between runs and dev data is never at risk.

Then run the suite:

```bash
./vendor/bin/sail artisan test
```

or directly with PHPUnit:

```bash
./vendor/bin/sail exec laravel.test vendor/bin/phpunit
```

### CI

[`.github/workflows/tests.yml`](.github/workflows/tests.yml) runs the suite on every push and pull request to `master` (and can be triggered manually). It spins up a throwaway MySQL 8.4 service container seeded with the same `laravel_api_test` database/credentials as `.env.testing`, overriding only `DB_HOST`/`DB_PORT` since the service isn't reachable at the `mysql` hostname outside of Sail. It also collects coverage (via `pcov`) and uploads it to [Codecov](https://codecov.io/gh/Zenich75/wtgtest).

> The Codecov badge only populates once the repo is enabled at [codecov.io](https://codecov.io) (sign in with GitHub, add the repo) and, for reliable uploads, a `CODECOV_TOKEN` repo secret is set from the token shown there.

[`.github/workflows/pint.yml`](.github/workflows/pint.yml) runs `vendor/bin/pint --test` on the same triggers to check code style without modifying files.

## API Documentation

- **Swagger / OpenAPI UI**: `{APP_URL}/api/documentation` (e.g. `http://localhost/api/documentation`)
- **Raw OpenAPI spec**: `{APP_URL}/docs`
- **Postman collection**: [docs/postman_collection.json](docs/postman_collection.json) — includes a "Full Flow" folder that runs import → check status → search property → reserve offer sequentially. See its description for prerequisites (seeded supplier, running queue worker).

Regenerate the docs after changing any `#[OA\...]` annotations:

```bash
./vendor/bin/sail artisan l5-swagger:generate
```

## Data Browser

A read-only, server-rendered Blade interface for internal review of the underlying data — not part of the public API and has no authentication, so it should not be exposed outside a trusted environment.

- `GET /viewer/imports` — imports list (supplier, status badge, offer counts, timestamps), paginated, newest first.
- `GET /viewer/properties` — properties list with a count of offers per property, paginated.
- `GET /viewer/offers` — offers list, paginated, filterable via `?supplier=<code>&city=<city>` query params.
- `GET /viewer/reservations` — reservations list with status badges, paginated.

A "Browse data" link on the homepage (`/`) points into this section.

## Design Notes

### Import Idempotency

Imports are keyed by the combination of `supplier_id` and `external_import_id`, enforced by a unique composite database index on the `imports` table. `POST /api/imports` uses `Import::firstOrCreate(['supplier_id' => ..., 'external_import_id' => ...], [...])`: if a matching row already exists, it's returned as-is and no new `ProcessImportJob` is dispatched; only a genuinely new row triggers processing. This makes the endpoint safe to retry (e.g. after a network timeout) without creating duplicate imports or duplicate processing jobs.

Within `ProcessImportJob`, each offer in the payload is upserted with `Offer::updateOrCreate(['supplier_id' => ..., 'external_id' => ...], [...])`, backed by a unique composite index on `offers.supplier_id` + `offers.external_id`. Reprocessing the same offer (e.g. a supplier resending a batch, or a corrected price) updates the existing row in place instead of creating a duplicate. Properties are similarly deduplicated via `Property::firstOrCreate(['code' => ...], [...])`.

### Reservation Concurrency Safety

`POST /api/offers/{offer}/reservations` must prevent overselling an offer's `available_units` when multiple reservation requests race against each other. This is handled by `ReservationService`:

1. A `DB::transaction()` wraps the whole operation.
2. Inside the transaction, the offer row is re-fetched with `lockForUpdate()`. This issues a `SELECT ... FOR UPDATE`, which takes a row-level lock: any other transaction trying to read the same offer row with `lockForUpdate()` blocks until this transaction commits or rolls back.
3. `available_units` is checked against the **locked, current** value (not a value read earlier in the request), so a concurrent request can't observe a stale count.
4. If units are available, the count is decremented and the `Reservation` is created; if not, the transaction ends without any writes and the endpoint returns `409 Conflict`.

Because the check-and-decrement happens on a locked row inside one transaction, two simultaneous requests for the last remaining unit are serialized by the database: the first to acquire the lock succeeds and commits its decrement, and the second then sees `available_units = 0` and is correctly rejected — no matter how closely the requests overlap in time.

<!-- ci: verifying pull_request trigger, will be removed -->
