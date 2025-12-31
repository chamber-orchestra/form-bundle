# Repository Guidelines

## Project Structure & Module Organization
- `src/` contains the bundle source under the `ChamberOrchestra\FormBundle` namespace.
- `src/Type/`, `src/View/`, `src/Transformer/`, and `src/Validator/` hold core extension points.
- `tests/` holds PHPUnit tests; `tests/Integrational/TestKernel.php` boots the test kernel.
- `src/Resources/config/services.yaml` defines bundle service wiring.

## Build, Test, and Development Commands
- `composer install` installs PHP dependencies into `vendor/`.
- `composer update` refreshes dependency versions per `composer.json`.
- `./vendor/bin/phpunit` runs the test suite using `phpunit.xml.dist`.

## Coding Style & Naming Conventions
- PHP 8.4 with `declare(strict_types=1);` at the top of PHP files.
- PSR-4 autoloading: `ChamberOrchestra\FormBundle\` maps to `src/`.
- Class names use `PascalCase` (e.g., `UniqueFieldValidator`), methods and variables use `camelCase`.
- Keep one class/interface/trait per file, matching the filename.
- No formatter or linter is configured; follow existing code style (PSR-12 conventions).

## Testing Guidelines
- PHPUnit is the test framework; config lives in `phpunit.xml.dist`.
- Tests live under `tests/` with namespaces rooted at `Tests\`.
- Prefer descriptive test class names aligned with the subject under `src/` (e.g., `FooTypeTest`).
- Run tests locally before submitting changes: `./vendor/bin/phpunit`.

## Commit & Pull Request Guidelines
- Git history is minimal and does not define a commit message convention. Use clear, imperative summaries (e.g., "Add form transformer for JSON input").
- PRs should include a short description, test results, and any relevant context or screenshots for UI changes.
- Link related issues if they exist and call out any backward-compatibility concerns.

## Configuration Notes
- Runtime requirements are in `composer.json` (PHP 8.4, Symfony 8 components, Doctrine ORM).
- Test kernel is configured via `KERNEL_CLASS=Tests\Integrational\TestKernel` in `phpunit.xml.dist`.
