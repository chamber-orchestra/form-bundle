[![PHP Composer](https://github.com/chamber-orchestra/form-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/form-bundle/actions/workflows/php.yml)
[![Latest Stable Version](https://poser.pugx.org/chamber-orchestra/form-bundle/v)](https://packagist.org/packages/chamber-orchestra/form-bundle)
[![License](https://poser.pugx.org/chamber-orchestra/form-bundle/license)](https://packagist.org/packages/chamber-orchestra/form-bundle)
![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)
![Symfony 8](https://img.shields.io/badge/Symfony-8-000000?logo=symfony&logoColor=white)
![PHPStan Max](https://img.shields.io/badge/PHPStan-max-brightgreen?logo=php&logoColor=white)
![PHP-CS-Fixer](https://img.shields.io/badge/PHP--CS--Fixer-✓-brightgreen?logo=php&logoColor=white)

# ChamberOrchestra Form Bundle

A Symfony 8 bundle that simplifies JSON-first form handling for REST APIs. It provides controller traits for the submit/validate/response flow, specialized API form types with CSRF disabled, reusable data transformers, Doctrine-backed uniqueness validation, and structured error responses following [RFC 9457 (Problem Details for HTTP APIs)](https://www.rfc-editor.org/rfc/rfc9457).

## Features

- **Controller traits** (`FormTrait`, `ApiFormTrait`) for submit/validate/response flow with null-safe request handling
- **JSON payload handling** for mutation requests with automatic merging of uploaded files
- **API form base types** (`QueryForm`, `MutationForm`) with CSRF disabled and empty block prefixes for clean JSON payloads
- **Custom form types**: `BooleanType`, `TimestampType`, `HiddenEntityType` with secure query builder parameterization
- **Data transformers** for booleans, Unix timestamps, comma-separated arrays, and JSON strings
- **RFC 9457 problem details** via `ValidationFailedView` with structured violations for consistent API error responses
- **Translatable error normalizer** (`ProblemNormalizer`) for localized exception messages in problem detail responses
- **`UniqueField` validation constraint** for Doctrine repositories with multi-field checks, closure-based exclusions, and custom normalizers
- **`TelExtension`** to strip non-digit characters from phone number input
- **`CollectionUtils`** for syncing Doctrine collections (add new / remove stale items)

## Requirements

- PHP ^8.5
- Symfony 8.0 components (framework-bundle, form, validator, config, dependency-injection, runtime, translation, clock)
- Doctrine ORM 3.6
- [chamber-orchestra/view-bundle](https://github.com/chamber-orchestra/view-bundle) 8.0

## Installation

```bash
composer require chamber-orchestra/form-bundle:8.0.*
```

Enable the bundle in `config/bundles.php`:

```php
return [
    // ...
    ChamberOrchestra\FormBundle\ChamberOrchestraFormBundle::class => ['all' => true],
];
```

## Usage

### Controller Traits

Use `FormTrait` for standard HTML form submissions and `ApiFormTrait` for JSON API endpoints.

`handleFormCall()` accepts a form class or instance, handles the request, and returns a view or response. `handleApiCall()` does the same for API endpoints -- it automatically parses JSON payloads for `MutationForm` types and merges uploaded files.

`onFormSubmitted()` checks validity, returns a `ValidationFailedView` on failure, or invokes the callback on success:

```php
use ChamberOrchestra\FormBundle\ApiFormTrait;
use ChamberOrchestra\ViewBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

final class SearchCourseAction
{
    use ApiFormTrait;

    public function __invoke(Request $request): ViewInterface
    {
        $form = $this->createForm(SearchCourseForm::class);
        $form->submit($request->query->all());

        return $this->onFormSubmitted($form, function (SearchCourseData $dto) use ($request) {
            $entities = $this->er->searchCourses(
                $pagination = $this->getPagination(['per_page_limit' => $this->getPerPageLimit($request)]),
                $dto->query,
                $dto->brands,
                $dto->topics,
                $dto->products,
                $dto->durations
            );

            return new PaginatedView($entities, $pagination, CourseView::class);
        });
    }
}
```

### API Form Types

Extend `QueryForm` for GET requests or `MutationForm` for POST/PUT/PATCH requests. Both disable CSRF protection and use empty block prefixes for clean JSON input/output.

### Data Transformers

| Transformer | Description |
|---|---|
| `TextToBoolTransformer` | Converts `"true"`, `"1"`, `"yes"` to boolean |
| `DateTimeToNumberTransformer` | Converts Unix timestamps to `DateTimeInterface` objects |
| `ArrayToStringTransformer` | Converts arrays to/from comma-separated strings |
| `JsonStringToArrayTransformer` | Parses JSON strings to arrays (handles empty strings) |

### HiddenEntityType

Loads Doctrine entities by ID from a hidden form field. Supports a custom `query_builder` option with secure parameterized queries.

### UniqueField Validator

Validates field uniqueness against Doctrine repositories. Supports multiple fields, closure-based exclusions, custom normalizers, and targeted error paths.

### ProblemNormalizer

Extends Symfony's `ProblemNormalizer` to translate exception messages when the exception implements `TranslatableExceptionInterface`. This ensures localized error messages in RFC 9457 problem detail responses.

## Response Views

| View | HTTP Status | Description |
|---|---|---|
| `SuccessView` | 200 | Empty success response |
| `ValidationFailedView` | 422 | Form validation errors with structured `ViolationView` items |
| `FailureView` | Configurable | Generic error response |
| `RedirectView` | 301/302 | Redirect response for AJAX requests |
| `SuccessHtmlView` | 200 | HTML fragment response for AJAX requests |

## Testing

Install dependencies and run the full test suite:

```bash
composer install
./vendor/bin/phpunit
```

The integration test kernel (`tests/Integrational/TestKernel.php`) boots a minimal Symfony application with in-memory SQLite for Doctrine tests.

## License

MIT
