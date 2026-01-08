# Local Setup (Docker Environment)

This project is designed to run inside containers. The repository does **not** include Docker Compose files, so the steps below assume you already have containers for:

- PHP 8.2+ (PHP-FPM)
- MySQL 8.0
- Nginx
- Node 20 (for Vite)

## 1) Install Backend Dependencies

Run inside the PHP container:

```bash
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```

## 2) Configure Database

Update `.env` to point to your MySQL container. Example values:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=wellbeing
DB_USERNAME=wellbeing
DB_PASSWORD=secret
```

Then run migrations:

```bash
php artisan migrate
```

## 3) Install Frontend Dependencies

Run inside the Node container:

```bash
npm install
npm run build
```

## 4) Development Mode

For hot reloading during development:

```bash
npm run dev
```

If you want to use Laravel’s built-in server (useful outside Nginx), run:

```bash
php artisan serve
```

## 5) Demo Mode

Demo mode is enabled in production and local environments. Use the demo routes to create sample data:

- `GET /demo/start`
- `POST /demo/reset`
- `POST /demo/end`

## 6) Cleaning Old Demo Users

```bash
php artisan demo:purge --hours=12
```
