# Common Developer Tasks

## Run the Test Suite

```bash
php artisan test
```

## Create a New Check-In (Flow Overview)

1. Route hits `CheckInController@store`.
2. Validation via `StoreCheckInRequest`.
3. DTO mapping through `CheckInMapper`.
4. Business rules enforced in `CheckInService::createForUser`.

If you want to modify the creation rules, update `CheckInService`.

## Add a New Dashboard Summary Metric

1. Add a query or calculation in `CheckInQueryService`.
2. Include it in `DashboardService::buildOverview`.
3. Render it in the `resources/views/dashboard` templates.

## Adjust Demo Seed Behavior

Update `DemoSeeder::seed` to tweak the range of dates, scoring logic, or notes.

## Add a New Route + Controller

1. Define the route in `routes/web.php`.
2. Create a controller in `app/Http/Controllers`.
3. Keep controllers thin and delegate logic to a service class.

## Run the Queue Worker (Dev)

```bash
php artisan queue:listen --tries=1
```
