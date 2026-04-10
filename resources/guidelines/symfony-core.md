# Symfony Project Guidelines

## Framework Conventions

- Follow PSR-12 coding standards
- Use PHP 8.2+ features: readonly properties, enums, named arguments, match expressions, fibers
- Prefer constructor property promotion
- Use strict types: `declare(strict_types=1);` in every PHP file
- Use Symfony attributes over YAML/XML configuration where possible (`#[Route]`, `#[AsEventListener]`, `#[AsCommand]`, etc.)

## Dependency Injection

- Always use constructor injection — never fetch services from the container directly
- Type-hint interfaces when available (e.g., `LoggerInterface`, `CacheInterface`, `EntityManagerInterface`)
- Use `#[Autowire]` attribute for scalar parameters
- Use `#[TaggedIterator]` for injecting tagged services
- Services should be `final` by default unless inheritance is explicitly needed

## Controllers

- Keep controllers thin — delegate business logic to services
- Use `#[Route]` attributes on methods
- Return `Response` objects, not arrays (unless using `#[MapResponsePayload]`)
- Use `#[MapRequestPayload]` and `#[MapQueryString]` for input mapping
- Use `#[MapEntity]` instead of manual entity lookups

## Doctrine ORM

- Use PHP 8 attributes for entity mapping (`#[ORM\Entity]`, `#[ORM\Column]`, etc.)
- Keep entities as plain objects — no service dependencies
- Use repositories for all database queries
- Use migrations for schema changes: `bin/console doctrine:migrations:diff`
- Never call `flush()` inside a repository — let the caller control the unit of work

## Twig Templates

- Use Twig's strict mode
- Prefer `{{ }}` for output, `{% %}` for logic
- Keep logic minimal in templates — use Twig extensions for complex formatting
- Use `{{ path() }}` and `{{ url() }}` for route generation
- Use the `|trans` filter for translatable strings

## Security

- Use voters for authorization logic, not role checks in controllers
- Never store secrets in code — use environment variables and the secrets vault
- Use `#[IsGranted]` attribute for route-level authorization
- Always use parameterized queries (Doctrine handles this automatically)
- Use CSRF protection for all forms

## Testing

- Write tests for all new features and bug fixes
- Use PHPUnit with Symfony's `WebTestCase` for functional tests
- Use `KernelTestCase` for integration tests
- Mock external services, not internal ones
- Run tests with: `php bin/phpunit`

## Console Commands

- Use `#[AsCommand]` attribute
- Use `SymfonyStyle` for formatted output
- Return integer exit codes (`Command::SUCCESS`, `Command::FAILURE`)
- Use `InputArgument` and `InputOption` for parameters

## Error Handling

- Use custom exception classes extending `\RuntimeException` or `\LogicException`
- Let Symfony's error handler manage HTTP error responses
- Log errors with context: `$logger->error('message', ['key' => $value])`
- Use `#[MapResponseStatusCode]` for custom error status codes

## File Structure

- `src/Controller/` — HTTP controllers
- `src/Entity/` — Doctrine entities
- `src/Repository/` — Doctrine repositories
- `src/Service/` — Business logic services
- `src/EventListener/` — Event listeners
- `src/Command/` — Console commands
- `src/Form/` — Form types
- `src/Security/` — Voters, authenticators
- `templates/` — Twig templates
- `config/` — Configuration files
- `migrations/` — Doctrine migrations
- `tests/` — Test files
