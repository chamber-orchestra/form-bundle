# ChamberOrchestra Form Bundle

Symfony bundle that streamlines JSON‑first form handling for APIs. It provides helper traits for controller flow, specialized form types, reusable transformers, and standardized error views (RFC 7807 style) to keep API responses consistent.

## Features
- Controller helpers via `FormTrait` and `ApiFormTrait` for submit/validate/response flow.
- JSON and file payload handling for mutation requests.
- API form base types (`GetForm`, `PostForm`, `QueryForm`, `MutationForm`) with empty block prefixes.
- Custom form types: `BooleanType`, `TimestampType`, `HiddenEntityType`.
- Data transformers for booleans, timestamps, arrays, and JSON strings.
- Problem/validation views and violation mapping for structured error output.
- `UniqueField` validation constraint for Doctrine repositories.
- `TelType` extension to normalize phone input.

## Requirements
- PHP 8.4
- Symfony 8.0 components (framework-bundle, form, validator, serializer, config, dependency-injection, runtime)
- doctrine/orm 3.6.*
- chamber-orchestra/view-bundle 8.0.*

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

## Usage Overview
- Controller flow helpers live in `src/FormTrait.php` and `src/ApiFormTrait.php`.
- Service wiring lives in `src/Resources/config/services.php`.
- Form types are under `src/Type/`, transformers under `src/Transformer/`.

## ApiFormTrait Example
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

## Tests
Install dependencies:
```bash
composer install
```

Run the full suite:
```bash
./vendor/bin/phpunit
```

The test kernel for integration tests is `tests/Integrational/TestKernel.php`.
