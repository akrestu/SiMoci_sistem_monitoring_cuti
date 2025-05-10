# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build Commands
- `php artisan serve` - Start the Laravel development server
- `npm run dev` - Start Vite dev server for frontend assets
- `composer run-script dev` - Run development server with queue listener and logs
- `composer test` - Run all tests
- `php artisan test --filter=TestName` - Run a specific test

## Lint/Style Commands
- `./vendor/bin/pint` - Run Laravel Pint for code style fixing
- `php artisan route:list` - List all registered routes

## Code Style Guidelines
- PSR-4 autoloading standard
- Class files should use PascalCase
- Method names use camelCase
- Use type declarations for method parameters and returns when possible
- Document properties with PHPDoc comments
- Group imports by type (PHP core, framework, app)
- Model relationships defined after model properties
- Validation rules included directly in controller methods
- Use response helpers (redirect, view, json) for consistent returns
- Error handling with standard Laravel validation responses
- Wrap HTML entities in blade views with translation function