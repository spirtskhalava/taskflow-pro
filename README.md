# TaskFlow Pro

A modern, full-stack project management SaaS application built with **Laravel 11** and **Vue 3**.

## Tech Stack

### Backend
- **Laravel 11** — PHP framework
- **MySQL 8.0** — Primary database
- **Redis** — Caching & queue driver
- **Laravel Sanctum** — SPA authentication
- **Laravel Echo + Pusher** — Real-time events
- **Laravel Horizon** — Queue monitoring
- Repository Pattern + Service Layer architecture

### Frontend
- **Vue 3** (Composition API + `<script setup>`)
- **TypeScript**
- **Pinia** — State management
- **Vue Router 4** — Client-side routing
- **Tailwind CSS** — Utility-first styling
- **Axios** — HTTP client

## Features

- **Authentication** — Register, login, email verification, password reset
- **Project Management** — Create, assign, archive projects with deadlines
- **Task Board** — Kanban-style board (To Do / In Progress / In Review / Done)
- **Team Collaboration** — Invite members, role-based permissions (Owner / Admin / Member)
- **Comments** — Threaded comments on tasks with @mentions
- **File Attachments** — Upload files to tasks (S3-compatible storage)
- **Activity Log** — Full audit trail per project
- **Notifications** — Real-time in-app + email notifications
- **Dashboard** — Stats, recent activity, assigned tasks
- **API** — RESTful JSON API with full documentation

## Architecture

```
taskflow-pro/
├── backend/              # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/API/
│   │   │   ├── Middleware/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Policies/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   └── Jobs/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── tests/
└── frontend/             # Vue 3 SPA
    └── src/
        ├── stores/       # Pinia stores
        ├── views/
        ├── components/
        ├── composables/
        ├── services/
        └── router/
```

## Setup

### Prerequisites
- PHP 8.2+
- Node.js 20+
- MySQL 8.0+
- Redis

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
cp .env.example .env.local
npm run dev
```

## API Documentation

API docs are available at `/api/documentation` when running locally (L5-Swagger).

### Key endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login |
| POST | `/api/auth/logout` | Logout |
| GET | `/api/projects` | List projects |
| POST | `/api/projects` | Create project |
| GET | `/api/projects/{id}/tasks` | List tasks |
| POST | `/api/projects/{id}/tasks` | Create task |
| PATCH | `/api/tasks/{id}/status` | Update task status |
| GET | `/api/dashboard` | Dashboard stats |

## Testing

```bash
# Backend
cd backend
php artisan test --coverage

# Frontend
cd frontend
npm run test:unit
```

## License

MIT
