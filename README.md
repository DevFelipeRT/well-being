# Well-Being Check-Ins

A Laravel-based MVP for tracking daily well-being check-ins. Users log a score and optional note once per day, then review summaries and trends on a dashboard. The project includes a production-facing demo mode so hiring managers can explore the experience without registration.

## Highlights

- Daily check-ins with a one-per-day rule and optional notes.
- Dashboard summaries (last 7/30 days, current month) and trend insights.
- Demo mode that auto-creates a temporary user and seeds sample data.
- Standard authentication flows powered by Laravel Breeze.

## Tech Stack

- Backend: PHP 8.2+ with Laravel 12.
- Database: MySQL 8.0 (primary target).
- Frontend tooling: Vite, Tailwind CSS, Alpine.js.

## Quickstart (Docker-based environment)

The repository does not include Docker Compose files, so use the scripts below within your existing Docker setup (PHP-FPM + MySQL + Nginx + Node). If you already mount the repo into containers, run the commands in the PHP and Node containers as appropriate.

```bash
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php artisan migrate
npm install
npm run build
```

For local development with hot reloading, run:

```bash
npm run dev
php artisan serve
```

## Demo Mode (Production-Visible)

Demo routes are intentionally enabled so the system can be viewed without registration. Use the demo start endpoint to create a temporary user and seed sample check-ins.

- Start demo: `GET /demo/start`
- Reset demo data: `POST /demo/reset`
- End demo: `POST /demo/end`

A purge command exists to remove demo users older than a configurable threshold.

```bash
php artisan demo:purge --hours=12
```

## Documentation

- [Project overview](docs/overview.md)
- [Architecture](docs/architecture.md)
- [Local setup](docs/how-to-local-setup.md)
- [Common tasks](docs/how-to-common-tasks.md)
- [Troubleshooting](docs/troubleshooting.md)
- [Architectural decisions](docs/decisions)

## License

MIT
