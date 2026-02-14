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


## Code Conventions

- PSR-12 style, `declare(strict_types=1)` in every file, 4-space indent
- View classes end with `View` suffix; utilities use verb naming (`BindUtils`, `ReflectionService`)
- Typed properties and return types; favor `readonly` where appropriate
- JSON structures should be explicit — avoid leaking nulls
- Namespace: `ChamberOrchestra\FormBundle\*` (PSR-4 from `src/`)
- Class names use `PascalCase` (e.g., `UniqueFieldValidator`), methods and variables use `camelCase`.
- Follow a consistent formatting style.
- Use clear, descriptive names for variables, functions, and classes.
- Avoid non-standard abbreviations.
- Each function should have a single, well-defined responsibility.

## Testing

- PHPUnit 12.x; tests in `tests/` autoloaded as `Tests\`
- **Unit tests** (`tests/Unit/`) extend `TestCase`; mirror source structure
- **Integration tests** (`tests/Integrational/`) extend `KernelTestCase`; use `Tests\Integrational\TestKernel` (minimal kernel with FrameworkBundle + ChamberOrchestraViewBundle)
- Tests reset `BindUtils` and `ReflectionService` static state between runs
- Use data providers for mapping scenarios and cache behavior
- Write code that is easy to test.
- Avoid hard dependencies; use dependency injection where appropriate.
- Do not hardcode time, randomness, UUIDs, or global state.

## Commit Style

Short, action-oriented messages with optional bracketed scope: `[fix] ensure nulls are stripped`, `[master] bump version`. Keep commits focused; avoid unrelated formatting churn.

## General Coding Principles

- Write production-quality code, not illustrative examples.
- Prefer simple, readable solutions over clever ones.
- Avoid premature optimization.
- Do not introduce architectural complexity without clear justification.
- Follow Symfony bundle and directory conventions.
- Use Dependency Injection; never fetch services from the container.
- Do not use static service locators.
- Prefer configuration via services.yaml over hardcoding.
- Use autowiring and autoconfiguration where possible.
- Follow PSR-12 coding standards.
- Use strict types.
- Prefer typed properties and return types everywhere.
- Avoid magic methods unless explicitly required.
- Do not rely on global state or superglobals.

## Structure and Architecture

- Separate business logic, infrastructure, and presentation layers.
- Do not mix side effects with pure logic.
- Minimize coupling between modules.
- Prefer composition to inheritance.
- Services must be small and focused.
- One class — one responsibility.
- Constructor injection only.
- Do not inject the container itself.
- Prefer interfaces for public-facing services.


## Error Handling and Edge Cases

- Handle errors explicitly.
- Never silently swallow exceptions.
- Validate all inputs.
- Consider edge cases and empty or null values.
- Use domain-specific exceptions.
- Do not catch exceptions unless you can handle them meaningfully.
- Fail fast on invalid state.
- Write code that is unit-testable by default.
- Avoid hard dependencies on time, randomness, or static state.
- Use interfaces or abstractions for external services.

## Performance and Resources

- Avoid unnecessary allocations and calls.
- Prevent N+1 queries.
- Assume the code may run on large datasets.

## Documentation and Comments

- Do not comment obvious code.
- Explain *why*, not *what*.
- Add comments when logic is non-trivial.
- Document public services and extension points.
- Comment non-obvious decisions, not implementation details.

## Working with Existing Code

- Preserve the existing codebase style and conventions.
- Do not refactor unrelated code.
- Make the smallest change necessary.

## Assistant Behavior

- Ask clarifying questions if requirements are ambiguous.
- If multiple solutions exist, choose the best one and briefly justify it.
- Avoid deprecated or experimental APIs unless explicitly requested.

## Backward Compatibility

- Do not introduce BC breaks without explicit instruction.
- Follow Symfony bundle versioning and deprecation practices.
