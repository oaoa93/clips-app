# Clips App

Small full-stack monorepo to manage video clips.

## Stack

- Backend: Laravel 12 + Sanctum + SQLite
- Frontend: Vue 3 (Composition API) + Pinia + Axios
- Microservice: Node.js + Express

## Project Structure

- `backend/`: REST API with token authentication and clips CRUD
- `frontend/`: SPA that consumes the Laravel API
- `microservice/`: independent service to process clip payloads

## Features

### Backend (Laravel)

- Sanctum auth endpoints: register, login, logout
- Clip model with fields:
  - `title`
  - `description`
  - `url`
  - `status` (`active` or `inactive`)
  - timestamps
- REST endpoints:
  - `GET /api/clips` (supports `?status=active|inactive`)
  - `POST /api/clips`
  - `PUT /api/clips/{id}`
  - `DELETE /api/clips/{id}`
- Form Request validation for auth and clip operations
- Factory + seeders with sample data
- Bonus job: `ProcessClipJob` dispatched when a clip is created

### Frontend (Vue 3)

- Composition API only (no Options API)
- Token-based auth flow (login/register/logout)
- Main dashboard with:
  - clip list
  - real-time filter by title and status
  - create/edit form with basic client validation
- State management with Pinia (bonus)

### Microservice (Node.js)

- `POST /api/process-clip` endpoint
- Receives clip-like payload and returns processed data:
  - slug generated from title
  - estimated duration (seconds)
  - normalized URL
  - basic metadata

## Requirements

- PHP 8.3+
- Composer 2+
- Node.js 20+
- npm 10+

## Installation

Clone and enter the project directory:

```bash
git clone <your-repo-url>
cd clips-app
```

### Fast path (recommended)

Use the root `Makefile` to avoid running multiple commands manually.

First time setup:

```bash
make setup
```

Start all services together (backend, queue worker, frontend, microservice):

```bash
make dev
```

Other useful targets:

```bash
make test
make build
make check
make reset-db
```

### 1) Backend setup

```bash
cd backend
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
```

Run backend:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### 2) Frontend setup

In a new terminal:

```bash
cd frontend
npm install
cp .env.example .env
```

Run frontend:

```bash
npm run dev
```

The frontend expects the backend API at `http://localhost:8000/api`.

### 3) Microservice setup

In a new terminal:

```bash
cd microservice
npm install
cp .env.example .env
```

Run microservice:

```bash
npm run dev
```

Default URL: `http://localhost:3001`

## Default Seeded User

After `php artisan migrate --seed`, you can login with:

- Email: `demo@example.com`
- Password: `password123`

## API Quick Reference

### Auth

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (Bearer token required)

### Clips

- `GET /api/clips`
- `GET /api/clips?status=active`
- `POST /api/clips`
- `PUT /api/clips/{id}`
- `DELETE /api/clips/{id}`

## Microservice Example

```bash
curl -X POST http://localhost:3001/api/process-clip \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Laravel jobs intro",
    "description": "A short walkthrough about queue workers",
    "url": "https://example.com/videos/laravel-jobs",
    "status": "active"
  }'
```

## Quality Checks

### Backend tests

```bash
cd backend
php artisan test
```

### Frontend build

```bash
cd frontend
npm run build
```

### Microservice smoke test

```bash
cd microservice
npm start
```
