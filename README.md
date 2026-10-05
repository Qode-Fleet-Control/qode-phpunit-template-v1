# PHPUnit template

Provisioned from [`Qode-Fleet-Control/fleet-template-v1`](https://github.com/Qode-Fleet-Control/fleet-template-v1) — the fleet
lifecycle contract (`bin/`, `fleet.conf`, `compose.yaml`, deploy workflows) with a
PHPUnit 13 starter laid on top. **This repo is a job, not a service.**

## Origin

PHPUnit ships no project generator; this follows its "Getting Started" guide, with each
file it can generate produced by the tool itself:

    docker run --rm -u $(id -u):$(id -g) -v "$PWD":/w -w /w <php8.4 + composer:2 image> sh -c '
      composer init --no-interaction --name=qode/phpunit-template --type=project \
        --description="PHPUnit testing template" --autoload=src/ --stability=stable &&
      composer require --dev --no-interaction phpunit/phpunit &&
      printf "\n\n\n\n" | vendor/bin/phpunit --generate-configuration'   # all defaults

Generated 2026-10-05 (phpunit/phpunit ^13.4, PHP 8.4.26). `src/` and `tests/` are
hand-written: the guide's `Greeter` example plus a `Calculator` showing a data provider,
`setUp()` and exception expectations. `vendor/` was removed; `composer.lock` is kept.

## Run it

**On the fleet** — `bin/run` (docker runtime) runs `docker compose build` and stops
there: `DOCKER_START_CMD` is empty because nothing listens on `$PORT`. Run the suite with

    docker compose run --rm app        # exits 0 when every test passes

**Without docker** (PHP 8.4+, composer):

    composer install
    vendor/bin/phpunit                 # or: composer test

| step | process runtime | docker runtime |
|---|---|---|
| install | `composer install --no-interaction` | — |
| build | — | `docker compose build` |
| start | (none — not a service) | (none — `docker compose run --rm app` runs the job) |

## How the container works

- `Dockerfile`: `php:8.4-cli-alpine` with composer, dev dependencies installed (PHPUnit
  is one), `php.ini-development` enabled, non-root user `app`; `CMD vendor/bin/phpunit`.
- `phpunit.xml` is the generated one: strict (`failOnRisky`, `failOnWarning`,
  `requireCoverageMetadata` — hence `#[CoversClass]` on every test class) and
  `warnWhenPhpIsNotConfiguredForDevelopment`, which is why the image uses
  `php.ini-development`.

## Deviations from the stock generator output, and why

- `composer.json`: `autoload-dev` for `tests/` and a `test` script (`phpunit`) added;
  `composer.lock` refreshed with `composer update --lock` to match.
- Added `Dockerfile`, `compose.yaml` (no ports), `.dockerignore`, `.gitignore`,
  `fleet.conf`, `bin/`, `.github/workflows/`, `docs/fleet-lifecycle.md`.

## If you add HTTP

Fleet apps are served at the root of their own hostname. Listen on `0.0.0.0:$PORT`, then
set `PORT`, `HEALTH_PATH`, `START_CMD` and `DOCKER_START_CMD` in `fleet.conf` and publish
`"${PORT}:${PORT}"` in `compose.yaml`.

## Verified

**Not verified yet.** `docker compose build` + `docker compose run --rm app` was never
reached: on 2026-10-05 the shared docker host's disk sat at 0-2 GB free (98 GB volume at
99-100%) for more than three hours, below the 6 GB gate builds wait for. Run both before
trusting this template. What *was* checked: `php -l` on every file in `src/` and
`tests/` → clean; `composer validate` → valid.

See `docs/fleet-lifecycle.md` for the lifecycle scripts.
