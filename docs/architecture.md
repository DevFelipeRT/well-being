# Architecture

## High-Level Structure

Laravel MVC with a service layer that separates write-side use cases from read-side queries.

```
HTTP Routes
  -> Controllers
     -> Services (application orchestration)
        -> Models (Eloquent ORM)
  -> Views (Blade)
```

## Key Modules

### Check-In Domain

- **CheckIn model**: encapsulates persistence, scopes, and basic helpers.
- **CheckInService**: write-side rules (one check-in per day, ownership enforcement).
- **CheckInQueryService**: read-side reporting (summaries, trends, distributions).

This split keeps mutation rules distinct from analytics queries and makes the dashboard easier to evolve independently.

### Dashboard

- **DashboardService** aggregates summaries and recent activity for the authenticated user.
- **DashboardController** renders the overview.

### Demo Mode

- **DemoController** handles demo session lifecycle.
- **DemoSeeder** creates realistic sample check-ins.
- **demo:purge** command removes old demo accounts and their check-ins.

### Auth & Policies

- Laravel Breeze provides registration, login, password reset, and verification routes.
- Policies restrict check-ins to their owners.

## Data Integrity & Constraints

- Database constraint ensures **one check-in per user per day**.
- Check-in scores are constrained to **1–5** (via DB check constraint where supported).
- Services double-check ownership and day uniqueness before writes.

## Extensibility Notes

- New analytics can be added by extending `CheckInQueryService` without altering write flows.
- New user-facing features should remain controller-thin and use services for orchestration.
- Demo behavior can be updated by adjusting `DemoSeeder` while leaving routes intact.
