.PHONY: up down build restart logs shell sf db-migrate db-fixtures test

## ── Docker ────────────────────────────────────────────────────────────────────
up:          ## Start all containers
	docker compose up -d

down:        ## Stop all containers
	docker compose down

build:       ## Rebuild images
	docker compose build --no-cache

restart:     ## Restart all containers
	docker compose restart

logs:        ## Tail logs
	docker compose logs -f

## ── Backend ───────────────────────────────────────────────────────────────────
shell:       ## Open a shell in the PHP container
	docker compose exec php sh

sf:          ## Run a Symfony console command — usage: make sf CMD="cache:clear"
	docker compose exec php php bin/console $(CMD)

install:     ## Install Composer dependencies
	docker compose exec php composer install

db-migrate:  ## Run database migrations
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

db-fixtures: ## Load development fixtures
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction --append

db-reset:    ## Drop, create, migrate, and load fixtures
	docker compose exec php php bin/console doctrine:database:drop --force --if-exists
	docker compose exec php php bin/console doctrine:database:create
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

jwt-keys:    ## Generate JWT key pair
	docker compose exec php php bin/console lexik:jwt:generate-keypair

test:        ## Run PHPUnit test suite
	docker compose exec php php bin/phpunit

## ── Frontend ──────────────────────────────────────────────────────────────────
fe-shell:    ## Open a shell in the frontend container
	docker compose exec frontend sh

fe-install:  ## Install npm dependencies
	docker compose exec frontend npm install

fe-build:    ## Build Next.js for production
	docker compose exec frontend npm run build
