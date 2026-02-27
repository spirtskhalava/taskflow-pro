# TaskFlow Pro

Built this as a freelance project for a client who needed something lighter than Jira but more structured than a shared spreadsheet. Tasks, projects, teams, a kanban board. Nothing groundbreaking feature-wise, but the client wanted it done properly: clean API, role-based access, real-time updates, the works.

Backend is Laravel 11 with Sanctum for auth, Redis for queues and cache, and Pusher for real-time updates when someone moves a task. Frontend is Vue 3 with the Composition API, Pinia, and TypeScript throughout.

## Stack

**Backend:** Laravel 11, MySQL 8, Redis, Sanctum, Horizon, Pusher  
**Frontend:** Vue 3, TypeScript, Pinia, Vue Router, Tailwind CSS, Vite

## Running locally

You'll need PHP 8.2+, Node 20+, MySQL and Redis running. Or just use the Docker setup below.

```bash
# backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

# frontend (separate terminal)
cd frontend
npm install
cp .env.example .env.local
npm run dev
```

The seeder creates a `demo@taskflow.dev` / `password` account with a few projects and tasks to click around.

### Docker

```bash
docker compose up -d
```

Spins up the app, MySQL, Redis, and a Horizon worker. Frontend still needs `npm run dev` locally since hot reload doesn't play well inside the container.

## How it's structured

The backend follows a repository pattern — controllers are thin and delegate to a service layer, services talk to repositories, repositories handle the Eloquent queries. Overkill for a small project but that was the point.

```
backend/app/
├── Http/
│   ├── Controllers/API/   # thin, just auth + response formatting
│   ├── Requests/          # validation lives here
│   └── Resources/         # API transformers with conditional eager loading
├── Services/              # business logic
├── Repositories/          # data access, all Eloquent queries here
├── Policies/              # authorization
├── Events/ + Listeners/   # task status changes broadcast over Pusher
└── Jobs/                  # notifications sent async via Redis queue
```

Frontend composables wrap the Pinia stores and handle error states so the views stay clean. The kanban board uses native HTML5 drag and drop with an optimistic update — status changes in the UI immediately and rolls back if the API call fails.

## Tests

```bash
cd backend && php artisan test --coverage
```

Feature tests cover auth, project CRUD with policy checks, and task status transitions. Unit tests use Mockery to isolate the service layer.

## Notes

- Real-time updates require Pusher credentials in `.env`. Without them everything still works, the frontend falls back to polling every 30 seconds.
- File attachments are stored locally by default (`storage/app/public`). Swap `FILESYSTEM_DISK=s3` and set the AWS keys to use S3.
- `php artisan horizon` needs to be running for email notifications to go out.
