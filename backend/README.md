# Backend - Laravel API

This module exposes the REST API for authentication and clip management.

## Features

- token authentication with Sanctum
- register, login, and logout
- clip CRUD protected with `auth:sanctum`
- status filter in `GET /api/clips?status=active|inactive`
- validation using Laravel Form Requests
- example data through seeders
- `ProcessClipJob` dispatched when creating a clip

## Technologies

- Laravel 12
- Laravel Sanctum
- SQLite
- PHPUnit

## Requirements

- PHP 8.2+ (8.3+ recommended)
- Composer 2+

## Installation

```bash
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
```

Demo user created by seed:

- Email: `demo@example.com`
- Password: `password123`

## Run

Start API:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Start queue worker:

```bash
php artisan queue:work --tries=1
```

## Endpoints

Base URL: `http://127.0.0.1:8000/api`

Authentication:

- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/logout` (Bearer token)
- `GET /user` (Bearer token)

Clips:

- `GET /clips`
- `GET /clips?status=active`
- `POST /clips`
- `PUT /clips/{id}`
- `DELETE /clips/{id}`

All clip endpoints require:

```text
Authorization: Bearer <TOKEN>
```

## curl Examples

Login:

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "demo@example.com",
    "password": "password123"
  }'
```

Create clip:

```bash
curl -X POST http://127.0.0.1:8000/api/clips \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Mi clip",
    "description": "Descripcion de ejemplo",
    "url": "https://example.com/videos/mi-clip",
    "status": "active"
  }'
```

## Clip Validation Rules

- `title`: required, string, max 180
- `description`: required, string
- `url`: required, valid URL, max 2048
- `status`: required, `active` or `inactive`

## Processing Job

When creating a clip, the controller dispatches `ProcessClipJob`.

The job simulates processing and writes a log entry.

View logs:

```bash
tail -f storage/logs/laravel.log
```

## Tests

Run backend tests:

```bash
php artisan test
```

Main coverage:

- authentication flow
- clip CRUD and filtering
- `ProcessClipJob` dispatch on clip creation

## Important Files

- `routes/api.php`
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Api/ClipController.php`
- `app/Http/Requests/Auth/*`
- `app/Http/Requests/Clip/*`
- `app/Jobs/ProcessClipJob.php`
- `database/migrations/*`
- `database/seeders/*`
- `tests/Feature/*`
