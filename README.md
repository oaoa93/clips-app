# Clips App

Full-stack application to manage video clips.

## Quick Summary

- Backend API: Laravel 12 + Sanctum + SQLite
- Frontend: Vue 3 + Composition API + Pinia
- Microservice: Node.js + Express

Local services:

- API: `http://127.0.0.1:8000`
- Frontend: `http://127.0.0.1:5173`
- Microservice: `http://127.0.0.1:3001`

Demo credentials (seed):

- Email: `demo@example.com`
- Password: `password123`

## Architecture

```text
frontend (Vue 3 + Pinia) ---> backend API (Laravel + Sanctum + SQLite)

microservice (Node.js + Express)
  `-- independent service to process clip data
```

## Project Structure

- `backend/`: authentication, REST API, clip CRUD, jobs, tests
- `frontend/`: SPA interface (login, list, filters, form)
- `microservice/`: clip payload transformation endpoint
- `Makefile`: setup, development, and verification commands

## Requirements

- PHP 8.2+ (8.3+ recommended)
- Composer 2+
- Node.js 20+
- npm 10+
- GNU Make

## Quick Start

### 1) Prepare the environment

```bash
make setup
```

This does:

- dependency installation
- `.env` creation from `.env.example`
- SQLite creation
- migrations and seeders

### 2) Start everything

```bash
make dev
```

This starts:

- Laravel backend
- queue worker
- Vue frontend
- Node.js microservice

## Quick App Usage

1. Open `http://127.0.0.1:5173`
2. Sign in with the demo user
3. View the clip list
4. Filter by title and status
5. Create, edit, and delete clips

## Main API (Examples)

Get token:

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "demo@example.com",
    "password": "password123"
  }'
```

Use `Authorization: Bearer <TOKEN>` in the following requests.

List clips:

```bash
curl http://127.0.0.1:8000/api/clips \
  -H "Authorization: Bearer <TOKEN>"
```

Filter active clips:

```bash
curl "http://127.0.0.1:8000/api/clips?status=active" \
  -H "Authorization: Bearer <TOKEN>"
```

Create clip:

```bash
curl -X POST http://127.0.0.1:8000/api/clips \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Nuevo clip",
    "description": "Descripcion de ejemplo",
    "url": "https://example.com/videos/nuevo-clip",
    "status": "active"
  }'
```

Update clip:

```bash
curl -X PUT http://127.0.0.1:8000/api/clips/1 \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Clip actualizado",
    "description": "Descripcion actualizada",
    "url": "https://example.com/videos/clip-actualizado",
    "status": "inactive"
  }'
```

Delete clip:

```bash
curl -X DELETE http://127.0.0.1:8000/api/clips/1 \
  -H "Authorization: Bearer <TOKEN>"
```

## Microservice (Example)

```bash
curl -X POST http://127.0.0.1:3001/api/process-clip \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Introduccion a Laravel Jobs",
    "description": "Video corto sobre jobs y workers",
    "url": "https://example.com/videos/laravel-jobs",
    "status": "active"
  }'
```

Example response:

```json
{
  "data": {
    "slug": "introduccion-a-laravel-jobs",
    "estimatedDurationSeconds": 60,
    "normalizedUrl": "https://example.com/videos/laravel-jobs",
    "titleLength": 29,
    "status": "active",
    "metadata": {
      "hasDescription": true,
      "sourceHost": "example.com"
    }
  }
}
```

## Useful Commands

```bash
make help
make setup
make dev
make test
make build
make check
make reset-db
```

## Module Documentation

- `backend/README.md`
- `frontend/README.md`
- `microservice/README.md`

## Troubleshooting

- If a port is busy, free `8000`, `5173`, or `3001`.
- If the demo user is missing, run `make reset-db`.
- If jobs are not processed, verify the worker is running in `make dev`.
- If frontend cannot connect to the API, check `frontend/.env` (`VITE_API_BASE_URL=http://localhost:8000/api`).
