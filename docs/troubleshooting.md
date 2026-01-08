# Troubleshooting

## Database Connection Errors

- Confirm `.env` values for `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
- Ensure the MySQL container is reachable from the PHP container (network/hostnames).

## Migration Failures

- Run `php artisan migrate:fresh` only in local/dev if you can drop data.
- Confirm MySQL 8.0 is used for the check constraint compatibility.

## Vite Build Issues

- Reinstall Node dependencies: `rm -rf node_modules && npm install`.
- Ensure the Node container has access to the project volume.

## Auth or Session Issues

- Run `php artisan config:clear` after updating `.env`.
- Verify session cookie domain and HTTPS settings if using a reverse proxy.

## Demo Mode Not Creating Data

- Ensure you can access `GET /demo/start`.
- Check that MySQL is writable and migrations are applied.
