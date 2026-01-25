# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Laravel Sail

This project uses **Laravel Sail** for local development. All PHP commands must be run through Sail:

```bash
# Instead of:
php artisan migrate
composer install
./vendor/bin/pest

# Use:
sail artisan migrate
sail composer install
sail pest
```

## Development Commands

```bash
# Start development (runs Laravel server, queue, logs, and Vite concurrently)
sail composer dev

# Run tests
sail composer test

# Run a single test file
sail pest tests/Feature/Auth/AuthenticationTest.php

# Run a specific test
sail pest --filter "test_name"

# Lint and format
npm run lint           # ESLint with auto-fix
npm run format         # Prettier format
npm run format:check   # Check formatting without fixing

# PHP code style
sail pint

# Build
npm run build          # Production build
npm run build:ssr      # SSR production build
```

## Architecture Overview

This is a **Laravel 12 + Vue 3 + Inertia** application with Laravel Fortify for authentication.

### Backend Structure

- **Controllers** (`app/Http/Controllers/`) - Organized by domain (e.g., `Settings/ProfileController.php`). Controllers render Inertia pages with props.
- **Actions** (`app/Actions/Fortify/`) - Business logic for authentication flows (user creation, password reset). Fortify contracts are implemented here.
- **Form Requests** (`app/Http/Requests/`) - All validation rules defined in request classes, not controllers.
- **Middleware** (`app/Http/Middleware/HandleInertiaRequests.php`) - Shares global props to all pages (auth user, sidebar state).
- **Providers** (`app/Providers/FortifyServiceProvider.php`) - Configures Fortify authentication views and rate limiting.

### Frontend Structure

- **Pages** (`resources/js/pages/`) - Inertia page components organized by domain (`auth/`, `settings/`).
- **Layouts** (`resources/js/layouts/`) - `AuthLayout.vue` for auth pages, `AppLayout.vue` for authenticated pages, `settings/Layout.vue` for settings.
- **Components** (`resources/js/components/`) - Reusable components. UI components in `ui/` are from shadcn-vue (Reka UI).
- **Composables** (`resources/js/composables/`) - Vue 3 composition functions for shared logic.
- **Routes** (`resources/js/routes/`) - Auto-generated type-safe route helpers via Wayfinder. Use `route.url()` for URLs, `route.form()` for Inertia form bindings.

### Routes

- **`routes/web.php`** - Main routes, includes `settings.php`
- **`routes/settings.php`** - Settings routes (profile, password, appearance, 2FA)
- Fortify routes are auto-registered (`/login`, `/register`, `/forgot-password`, etc.)

## Key Patterns

- **Wayfinder routes**: Import from `@/routes` for type-safe Laravel route references in Vue. Routes are auto-generated from PHP routes.
- **shadcn-vue components**: Add new UI components via `npx shadcn-vue@latest add <component>`. Components use `class-variance-authority` for variants.
- **Inertia forms**: Use `useForm` from `@inertiajs/vue3` with Wayfinder form helpers for type-safe form submissions.
- **Feature tests**: Located in `tests/Feature/`. Use Pest syntax. `RefreshDatabase` trait is auto-applied in Feature tests via `tests/Pest.php`.

## Code Style

- PHP: Laravel Pint (PSR-12 based)
- TypeScript/Vue: ESLint + Prettier with these settings:
  - Single quotes, semicolons required
  - 4-space indentation (2-space for YAML)
  - Tailwind class sorting via `prettier-plugin-tailwindcss`
