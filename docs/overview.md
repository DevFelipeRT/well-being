# Project Overview

## Purpose

Well-Being Check-Ins is an MVP for tracking daily well-being scores and short reflections. The goal is to capture a simple, consistent data point each day and provide a lightweight dashboard for insights over time.

## Scope

- **In scope**: daily check-ins (score + note), personal dashboard summaries, demo mode for public viewing, and standard auth/profile flows.
- **Out of scope**: multi-user team features, external integrations, and advanced analytics beyond the current summaries.

## Users and Roles

- **Guest (Demo)**: Can start a demo session that creates a temporary user and sample data.
- **Registered user**: Can create, update, view, and delete their own check-ins.

## Core Flows

1. **Daily check-in**
   - User records a score (1–5) and optional note.
   - System enforces one check-in per user per calendar day.

2. **Dashboard review**
   - Displays recent check-ins and summaries for the last 7/30 days and the current month.
   - Shows trend direction by comparing recent averages.

3. **Demo session**
   - `GET /demo/start` creates or resumes a demo user and seeds sample data.
   - `POST /demo/reset` clears demo check-ins and reseeds.
   - `POST /demo/end` logs out and clears the demo session.

## Data Model Summary

- **CheckIn**
  - `user_id` (owner)
  - `checked_at` (date)
  - `score` (1–5)
  - `note` (optional text)

## Assumptions & Constraints

- MySQL 8.0 is the primary database target.
- Demo mode is intentionally exposed in production for portfolio review.
- Data isolation is enforced at the query layer and via authorization policies.

## Open Questions

If you plan to expand the MVP, consider clarifying:

- Whether analytics should expand beyond current summaries and trend calculations.
- Whether demo sessions should be rate-limited or otherwise restricted.
