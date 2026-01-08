# ADR-0002: Demo Mode Available in Production

## Context

This project is an academic MVP and portfolio artifact. The primary objective is to allow reviewers to experience the product without creating an account. A demo mode already exists in the codebase and can seed realistic data.

## Decision

Keep demo routes enabled in production:

- `GET /demo/start`
- `POST /demo/reset`
- `POST /demo/end`

## Consequences

- Reviewers can access the product without registration.
- Demo users are ephemeral and isolated from real users.
- A scheduled cleanup command (`demo:purge`) removes stale demo users.
- Future production hardening may add rate limits or access controls if the app evolves beyond MVP scope.
