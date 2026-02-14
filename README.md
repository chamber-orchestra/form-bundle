[![PHP Composer](https://github.com/chamber-orchestra/form-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/form-bundle/actions/workflows/php.yml)

# ChamberOrchestra Form Bundle

A Symfony 8 bundle for JSON-first API form handling. Provides controller traits for submit/validate/response flow, specialized API form types, reusable data transformers, Doctrine-backed validation, and structured error responses following [RFC 9110](https://datatracker.ietf.org/doc/html/rfc9110#section-15).

## Features

- **Controller helpers** via `FormTrait` and `ApiFormTrait` for submit/validate/response flow with null-safe request handling.
- **JSON and file payload handling** for mutation requests with automatic merging of uploaded files.
- **API form base types** (`QueryForm`, `MutationForm`) with CSRF disabled and empty block prefixes for clean JSON payloads.
- **Custom form types**: `BooleanType`, `TimestampType`, `HiddenEntityType` with secure query builder parameterization.
- **Data transformers** for booleans, Unix timestamps, comma-separated arrays, and JSON strings.
- **RFC 9110 error views** with structured violations for consistent API error responses.
- **`UniqueField` validation constraint** for Doctrine repositories with field name validation and closure-based exclusions.
- **`TelExtension`** to normalize phone number input.
- **`CollectionUtils`** for syncing Doctrine collections.

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

Controller flow helpers live in `FormTrait` and `ApiFormTrait`. Use `handleFormCall()` for standard form submissions and `handleApiCall()` for JSON API endpoints.

### API Form Types

Extend `QueryForm` for GET requests or `MutationForm` for POST/PUT/PATCH requests. Both disable CSRF protection and use empty block prefixes for clean JSON input/output.

### Data Transformers

- `TextToBoolTransformer` -- converts `"true"`, `"1"`, `"yes"` to boolean
- `DateTimeToNumberTransformer` -- converts Unix timestamps to `DateTimeInterface` objects
- `ArrayToStringTransformer` -- converts arrays to/from comma-separated strings
- `JsonStringToArrayTransformer` -- parses JSON strings to arrays (handles empty strings)

### HiddenEntityType

Loads Doctrine entities by ID from a hidden form field. Supports custom `query_builder` with secure parameterized queries.

### UniqueField Validator

Validates field uniqueness against Doctrine repositories. Supports multiple fields, closure-based exclusions, custom normalizers, and targeted error paths.

## Example

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

## Testing

Install dependencies and run the full test suite:

```bash
composer install
./vendor/bin/phpunit
```

The integration test kernel (`tests/Integrational/TestKernel.php`) boots a minimal Symfony application with in-memory SQLite for Doctrine tests.

## License

MIT
