# Microservice - Clip Processor

Independent Node.js service to transform and enrich clip data.

## Features

- `POST /api/process-clip` endpoint
- basic payload validation
- `slug` generation from title
- estimated duration in seconds
- URL normalization
- useful metadata response

## Technologies

- Node.js
- Express

## Requirements

- Node.js 20+
- npm 10+

## Installation

```bash
npm install
cp .env.example .env
```

Available variable:

- `PORT=3001`

## Run

Development mode:

```bash
npm run dev
```

Normal mode:

```bash
npm start
```

Default base URL: `http://localhost:3001`

## Endpoints

### GET /health

Checks that the service is running.

```bash
curl http://localhost:3001/health
```

Response:

```json
{
  "status": "ok"
}
```

### POST /api/process-clip

Receives a clip-like object and returns processed data.

Example payload:

```json
{
  "title": "Introduccion a Laravel Jobs",
  "description": "Video corto sobre jobs y workers",
  "url": "https://example.com/videos/laravel-jobs",
  "status": "active"
}
```

Request:

```bash
curl -X POST http://localhost:3001/api/process-clip \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Introduccion a Laravel Jobs",
    "description": "Video corto sobre jobs y workers",
    "url": "https://example.com/videos/laravel-jobs",
    "status": "active"
  }'
```

Successful response:

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

Error response (example):

```json
{
  "message": "The title field is required."
}
```

## Processing Rules

- `title` is required
- allowed `status`: `active` or `inactive`
- if `status` is missing, default is `active`
- if `url` is invalid, returns `normalizedUrl: null`
- `estimatedDurationSeconds` is calculated from word count

## Important Files

- `index.js`: Express server and routes
- `src/clipProcessor.js`: validation and transformations
