SHELL := /usr/bin/env bash
.RECIPEPREFIX := >
.DEFAULT_GOAL := help

BACKEND_DIR := backend
FRONTEND_DIR := frontend
MICROSERVICE_DIR := microservice

.PHONY: help setup setup-backend setup-frontend setup-microservice setup-env setup-db dev test build check reset-db

help:
> @printf "Available targets:\n"
> @printf "  make setup      Install dependencies and bootstrap local environment\n"
> @printf "  make dev        Run backend, queue worker, frontend, and microservice\n"
> @printf "  make test       Run backend test suite\n"
> @printf "  make build      Build frontend for production\n"
> @printf "  make check      Run test + build\n"
> @printf "  make reset-db   Recreate SQLite database and seed data\n"

setup:
> $(MAKE) setup-backend
> $(MAKE) setup-frontend
> $(MAKE) setup-microservice
> $(MAKE) setup-env
> $(MAKE) setup-db
> @printf "Setup complete. Use 'make dev' to start all services.\n"

setup-backend:
> composer install --working-dir="$(BACKEND_DIR)"

setup-frontend:
> npm install --prefix "$(FRONTEND_DIR)"

setup-microservice:
> npm install --prefix "$(MICROSERVICE_DIR)"

setup-env:
> [ -f "$(BACKEND_DIR)/.env" ] || cp "$(BACKEND_DIR)/.env.example" "$(BACKEND_DIR)/.env"
> [ -f "$(FRONTEND_DIR)/.env" ] || cp "$(FRONTEND_DIR)/.env.example" "$(FRONTEND_DIR)/.env"
> [ -f "$(MICROSERVICE_DIR)/.env" ] || cp "$(MICROSERVICE_DIR)/.env.example" "$(MICROSERVICE_DIR)/.env"

setup-db:
> mkdir -p "$(BACKEND_DIR)/database"
> touch "$(BACKEND_DIR)/database/database.sqlite"
> if ! grep -qE '^APP_KEY=base64:' "$(BACKEND_DIR)/.env"; then (cd "$(BACKEND_DIR)" && php artisan key:generate --force); fi
> (cd "$(BACKEND_DIR)" && php artisan migrate --seed)

dev:
> @bash -eu -c '\
> if [ ! -f "$(BACKEND_DIR)/.env" ] || [ ! -f "$(FRONTEND_DIR)/.env" ] || [ ! -f "$(MICROSERVICE_DIR)/.env" ]; then \
>   printf "Missing .env files. Run make setup first.\n"; \
>   exit 1; \
> fi; \
> pids=""; \
> cleanup() { for pid in $$pids; do kill "$$pid" 2>/dev/null || true; done; }; \
> trap cleanup EXIT INT TERM; \
> printf "Starting services...\n"; \
> printf "  API         http://127.0.0.1:8000\n"; \
> printf "  Frontend    http://127.0.0.1:5173\n"; \
> printf "  Microservice http://127.0.0.1:3001\n"; \
> (cd "$(BACKEND_DIR)" && php artisan serve --host=127.0.0.1 --port=8000 --no-reload) & pids="$$pids $$!"; \
> (cd "$(BACKEND_DIR)" && php artisan queue:work --tries=1) & pids="$$pids $$!"; \
> (cd "$(FRONTEND_DIR)" && npm run dev -- --host 127.0.0.1 --port 5173) & pids="$$pids $$!"; \
> (cd "$(MICROSERVICE_DIR)" && npm run dev) & pids="$$pids $$!"; \
> wait -n $$pids; \
> '

test:
> (cd "$(BACKEND_DIR)" && php artisan test)

build:
> (cd "$(FRONTEND_DIR)" && npm run build)

check: test build

reset-db:
> (cd "$(BACKEND_DIR)" && php artisan migrate:fresh --seed)
