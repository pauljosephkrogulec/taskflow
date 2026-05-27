# TaskFlow

> A full-stack Task & Project Management application — portfolio project demonstrating DDD, JWT auth, API Platform, and Next.js SSR.

**Live demo:** _coming soon_

---

## Stack

| Layer        | Technology                                          |
|--------------|-----------------------------------------------------|
| Backend      | PHP 8.4 · Symfony 8.0 · API Platform 4.3            |
| Database     | PostgreSQL 17 · Doctrine ORM                        |
| Auth         | JWT (LexikJWTAuthenticationBundle) + refresh tokens |
| Frontend     | Next.js 16.2 · TypeScript · React · Tailwind CSS    |
| State        | React Query (TanStack Query v5)                     |
| Infra        | Docker · Docker Compose                             |

---

## Architecture

```
taskflow/
├── backend/                    # Symfony 8 API
│   └── src/
│       ├── Domain/             # Entities, VOs, repo interfaces, domain events
│       ├── Application/        # Use-case handlers (no framework deps)
│       ├── Infrastructure/     # Doctrine repos, security adapters
│       └── Api/                # API Platform resources, DTOs, Processors, Providers
│
└── frontend/                   # Next.js 16 App Router
    ├── app/
    │   ├── api/                # BFF routes (proxy auth, set httpOnly cookies)
    │   ├── dashboard/          # SSR project list (Server Component)
    │   ├── projects/[id]/      # SSR + React Query Kanban board
    │   ├── login/ register/    # Auth pages (Client Components)
    ├── components/             # UI components (TaskCard, KanbanBoard, Modal…)
    ├── lib/                    # API client, auth helpers
    └── types/                  # Shared TypeScript types
```

### Backend — DDD / Clean Architecture

| Layer | Rule |
|-------|------|
| `Domain` | Zero framework deps. Entities, VOs, repository interfaces, domain exceptions. |
| `Application` | Orchestrates domain objects. No Symfony/Doctrine imports. |
| `Infrastructure` | Doctrine implementations, `UserProvider` security adapter. |
| `Api` | API Platform resources, Processors, Providers, DTOs. Calls Application handlers. |

### Frontend rendering strategy

| Route | Strategy | Why |
|-------|----------|-----|
| `/dashboard` | SSR Server Component | Fresh auth-gated data, no loading flash |
| `/projects/[id]` | SSR + React Query | Kanban needs optimistic drag-and-drop updates |
| `/login`, `/register` | Client Component | Form interactivity |
| `/api/auth/*` | Next.js Route Handler | BFF proxy — sets httpOnly JWT cookies |

### Authentication flow

```
Browser  →  POST /api/auth/login  (Next.js BFF)
                →  POST /auth/login  (Symfony) → { token, refresh_token }
                ←  sets httpOnly cookies: tf_token (1h), tf_refresh (30d)

SSR Server Component  → reads tf_token cookie → Bearer token → Symfony API
Client Component      → fetch('/api/...') → Next.js BFF forwards cookie token
```

---

## Quick Start

**Prerequisites:** Docker + Docker Compose

```bash
git clone https://github.com/pauljosephkrogulec/taskflow.git
cd taskflow
cp .env.example .env   # review if needed
```

```bash
docker compose up -d
make jwt-keys          # generate JWT keypair (required once)
make db-migrate        # run DB migrations
make db-fixtures       # load dev seed data
make fe-install        # install npm dependencies
```

| Service | URL |
|---------|-----|
| API + Swagger UI | http://localhost:8080/api |
| Frontend | http://localhost:3000 |

### Dev seed users

| Email | Password | Role |
|-------|----------|------|
| alice@taskflow.dev | password | Admin |
| bob@taskflow.dev | password | User |
| charlie@taskflow.dev | password | User |

---

## API Reference

All endpoints except `/auth/*` require `Authorization: Bearer <token>`.

### Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/register` | Register → `{ id, email, name }` |
| `POST` | `/auth/login` | Login → `{ token, refresh_token }` |
| `POST` | `/auth/refresh` | Exchange refresh token |

### Projects

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/projects` | Caller's projects only |
| `POST` | `/api/projects` | Create project |
| `GET` | `/api/projects/{id}` | Project detail + members |
| `PATCH` | `/api/projects/{id}` | Rename (owner only) |
| `DELETE` | `/api/projects/{id}` | Archive (owner only) |

### Tasks

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/projects/{id}/tasks` | List tasks |
| `POST` | `/api/projects/{id}/tasks` | Create task |
| `GET` | `/api/tasks/{id}` | Task detail + embedded comments |
| `PATCH` | `/api/tasks/{id}` | Update task |
| `PATCH` | `/api/tasks/{id}/transition` | Status change (state machine enforced) |

**Status machine:** `todo → in_progress → review → done`  
Allowed back-transitions: `in_progress → todo`, `review → in_progress`

### Comments

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/tasks/{id}/comments` | Post comment (project members only) |

Interactive docs: http://localhost:8080/api

---

## Environment Variables

### Backend (`backend/.env`)

| Variable | Description |
|----------|-------------|
| `APP_SECRET` | Symfony app secret (≥32 chars) |
| `DATABASE_URL` | PostgreSQL connection DSN |
| `JWT_SECRET_KEY` | Path to private PEM key |
| `JWT_PUBLIC_KEY` | Path to public PEM key |
| `JWT_PASSPHRASE` | Keypair passphrase |
| `CORS_ALLOW_ORIGIN` | Allowed origins regex |

### Frontend (`frontend/.env.local`)

| Variable | Description |
|----------|-------------|
| `NEXT_PUBLIC_API_URL` | API URL for browser requests |
| `API_INTERNAL_URL` | API URL for SSR (Docker-internal) |

---

## Commands

```bash
make up / down / build / logs   # Docker lifecycle
make shell                      # PHP container shell
make sf CMD="cache:clear"       # Any Symfony console command
make db-reset                   # Drop → migrate → seed
make test                       # PHPUnit (32 tests)
make fe-shell / fe-install / fe-build

# Run a single test file
docker compose exec php php bin/phpunit tests/Unit/Domain/Task/TaskStatusTransitionTest.php
```

---

## Tech Choices & Trade-offs

| Decision | Rationale |
|----------|-----------|
| DDD + Clean Architecture | Business rules are framework-independent and unit-testable without DB |
| API Platform custom Processors | All writes go through Application handlers; avoids auto-CRUD magic |
| JWT stateless auth | Fits Docker/multi-container deployment; no session store needed |
| httpOnly cookie BFF | Prevents XSS token theft vs `localStorage`; Next.js route handlers act as proxy |
| SSR for dashboard | No loading flash; auth-gated on the server |
| React Query for Kanban | Optimistic drag-and-drop without full page reload |
| PHP enums for status/role | Native type safety; stored as readable strings in PostgreSQL |
| UUID string IDs | No integer sequence exposure; consistent across environments |
