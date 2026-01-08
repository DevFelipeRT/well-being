# ADR-0001: Separate Check-In Write and Read Services

## Context

The application handles two distinct needs:

1. Enforcing business rules for creating, updating, and deleting check-ins.
2. Producing summary analytics for the dashboard (averages, trends, distributions).

Mixing these concerns in a single service would make the code harder to test and extend, especially as analytics evolve.

## Decision

Split the domain logic into:

- **CheckInService** for write operations and business rules.
- **CheckInQueryService** for read/analytics operations.

## Consequences

- Write rules remain stable and focused on constraints.
- Read-side calculations can expand without impacting mutation logic.
- Controllers remain thin by delegating to purpose-built services.
