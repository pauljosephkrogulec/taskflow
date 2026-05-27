# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4 · Symfony 8.0 · API Platform 4.3 |
| Database | PostgreSQL 17 · Doctrine ORM |
| Auth | JWT (LexikJWTAuthenticationBundle) |
| Frontend | Next.js 16.2 · TypeScript · React · Tailwind CSS |
| Infra | Docker · Docker Compose |

## Common Commands

All development happens inside Docker containers. Use `make` targets or `docker compose exec` directly.

```bash
make up             # Start all containers
make down           # Stop all containers
make build          # Rebuild images (no cache)
make logs           # Tail all container logs

# Backend
make shell          # Shell into PHP container
make sf CMD="..."   # Run any Symfony console command
make install        # composer install
make db-migrate     # Run pending migrations
make db-reset       # Drop → create → migrate → fixtures (full reset)
make jwt-keys       # Generate JWT keypair (required on first setup)
make test           # Run full PHPUnit suite

# Run a single test
docker compose exec php php bin/phpunit tests/path/to/TestFile.php

# Frontend
make fe-shell       # Shell into frontend container
make fe-install     # npm install
make fe-build       # Production build
```

URLs after `make up`:
- API + Swagger UI: `http://localhost:8080/api`
- Frontend: `http://localhost:3000`

## Architecture

### Backend — DDD / Clean Architecture

```
backend/src/
  Domain/         # Entities, Value Objects, Repository interfaces, Domain Events
  Application/    # Use cases / Command & Query handlers (no framework deps)
  Infrastructure/ # Doctrine repositories, external services
  Api/            # API Platform Resources: DTOs, State Processors/Providers
```

- **Domain** layer has zero framework dependencies.
- **Application** layer orchestrates domain objects; no Symfony/Doctrine imports.
- **Api** layer maps HTTP requests to Application commands/queries via API Platform State Processors and Providers.
- Repository interfaces live in `Domain/`; Doctrine implementations live in `Infrastructure/`.

### Frontend — Next.js App Router

- Server Components handle SSR data fetching using `API_INTERNAL_URL` (Docker-internal `http://nginx`) to avoid extra network hops.
- Client Components use `NEXT_PUBLIC_API_URL` (`http://localhost:8080`) for browser-side requests.
- JWT tokens are managed client-side and passed as `Authorization: Bearer` headers.

### Environment Variables

Key variables (see `.env.example`):
- `APP_SECRET` — Symfony app secret (≥32 chars)
- `JWT_PASSPHRASE` — passphrase for the JWT key pair
- `DATABASE_URL` — auto-configured via Docker Compose

JWT keys must be generated before the API will work: `make jwt-keys`.
