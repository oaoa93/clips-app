# Frontend - Vue 3 Application

This module contains the web interface for authentication and clip management.

## Features

- Vue 3 with Composition API (`<script setup>`)
- state management with Pinia
- user login and registration
- clip list view
- real-time filtering by title and status
- create and edit form
- clip deletion

## Requirements

- Node.js 20+
- npm 10+

## Configuration

Install dependencies:

```bash
npm install
```

Create environment file:

```bash
cp .env.example .env
```

Main variable:

- `VITE_API_BASE_URL=http://localhost:8000/api`

## Run

Development mode:

```bash
npm run dev
```

Build for production:

```bash
npm run build
```

Preview build:

```bash
npm run preview
```

## Usage Flow

1. If no token exists, login/register is shown
2. After auth, user data and clips are loaded
3. User can filter by title and status
4. User can create clips
5. User can edit clips
6. User can delete clips
7. On logout, local state is cleared

## Client-side Validations

Authentication:

- valid email format
- required password
- required name on registration
- minimum password length of 8
- password confirmation

Clip form:

- required `title`
- required `description`
- valid `url` with `http` or `https`
- `status` only `active` or `inactive`

## State and Structure

Pinia stores:

- `src/stores/auth.js`: session, login, register, logout, current user
- `src/stores/clips.js`: list and clip CRUD operations

Key files:

- `src/App.vue`
- `src/lib/api.js`
- `src/components/AuthPanel.vue`
- `src/components/ClipList.vue`
- `src/components/ClipForm.vue`
- `src/components/BaseModal.vue`

## Technical Notes

- Token is stored in `localStorage` under `clips_token`.
- Axios automatically adds Bearer token when present.
- Frontend expects API at `http://localhost:8000` (configurable via `.env`).
