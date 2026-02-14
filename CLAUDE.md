# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

ChamberOrchestra Form Bundle is a Symfony bundle that streamlines JSON-first form handling for APIs. It provides controller helpers, specialized form types, data transformers, and RFC 7807-style error views for consistent API responses.

## Build and Test Commands

```bash
# Install dependencies
composer install

# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Unit/FormTraitTest.php

# Run tests in specific directory
./vendor/bin/phpunit tests/Unit/Transformer/
```

## Architecture

### Core Traits

**FormTrait** (`src/FormTrait.php`): Base trait for form handling in controllers. Provides methods for creating responses (`createSuccessResponse()`, `createValidationFailedResponse()`, `createRedirectResponse()`), handling form submission flow (`handleFormCall()`, `onFormSubmitted()`), and serializing form errors into structured violations.

**ApiFormTrait** (`src/ApiFormTrait.php`): Extends `FormTrait` for API controllers. Key method is `handleApiCall()` which automatically handles JSON payloads for `MutationForm` types and merges file uploads. Uses `convertRequestToArray()` to parse JSON content and merge with uploaded files.

### Form Type Hierarchy

**API Base Types** (`src/Type/Api/`):
- `GetForm`: For GET requests, sets method to GET
- `PostForm`: For POST requests, sets method to POST
- `QueryForm`: Extends `GetForm`, disables CSRF
- `MutationForm`: Extends `PostForm`, disables CSRF for JSON mutations

All API form types use empty `block_prefix` to avoid HTML name prefixes in JSON responses.

**Custom Types** (`src/Type/`):
- `BooleanType`: Text input that transforms to boolean via `TextToBoolTransformer`
- `TimestampType`: Integer input that transforms to DateTime via `DateTimeToNumberTransformer`
- `HiddenEntityType`: Hidden field that loads entities by ID from Doctrine repositories

### Data Transformers

Located in `src/Transformer/`:
- `TextToBoolTransformer`: Converts string values ("true", "1", "yes") to boolean
- `DateTimeToNumberTransformer`: Converts Unix timestamps to DateTime objects
- `ArrayToStringTransformer`: Converts arrays to comma-separated strings
- `JsonStringToArrayTransformer`: Parses JSON strings to arrays

### View Types

All views extend `ChamberOrchestra\ViewBundle\View\ViewInterface` (from chamber-orchestra/view-bundle):
- `FailureView`: Generic error response with HTTP status
- `ValidationFailedView`: Form validation errors (422 status) with structured violations
- `ViolationView`: Individual field violation with id, message, parameters, and path
- `RedirectView`: Redirect response for AJAX requests
- `SuccessHtmlView`: HTML fragment response for AJAX requests

### Validation

**UniqueField** constraint (`src/Validator/Constraints/`): Validates field uniqueness against Doctrine repositories. Use `repositoryMethod` to specify custom query method, `fields` to check multiple columns, and `errorPath` to target specific form field.

### Service Configuration

Services are autowired and autoconfigured via `src/Resources/config/services.php`. The config excludes `DependencyInjection`, `Resources`, `Exception`, `Transformer`, and `View` directories from auto-loading.

## Testing

- **Unit tests**: `tests/Unit/` - Test individual classes in isolation
- **Integration tests**: `tests/Integrational/` - Test bundle integration with Symfony and Doctrine
- **Test kernel**: `tests/Integrational/TestKernel.php` boots minimal Symfony application with FrameworkBundle, ChamberOrchestraViewBundle, ChamberOrchestraFormBundle, and optionally DoctrineBundle with in-memory SQLite

When writing tests, follow the existing pattern: Unit tests under `tests/Unit/` mirroring the `src/` structure, integration tests under `tests/Integrational/` for service wiring and Doctrine integration.

## Code Style

- PHP 8.5+ with strict types (`declare(strict_types=1);`)
- PSR-4 autoloading: `ChamberOrchestra\FormBundle\` → `src/`
- One class/interface/trait per file matching the filename
- Follow existing code formatting (PSR-12 conventions)

## Dependencies

- Requires Symfony 8.0 components, PHP 8.5, Doctrine ORM 3.6, and chamber-orchestra/view-bundle 8.0
- Main branch is `8.0` for Symfony 8 compatibility