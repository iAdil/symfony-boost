# Symfony Project Guidelines

## PHP Standards

- Use `declare(strict_types=1);` in every PHP file
- Follow PSR-12 coding standards strictly
- Use PHP 8.2+ features: readonly properties, enums, named arguments, match expressions, intersection types, `never` return type
- Prefer constructor property promotion for all services
- Use `final` on classes by default unless inheritance is explicitly designed
- Never use `@` error suppression operator
- Use `readonly` on properties that should not change after construction
- Prefer `match` over `switch` for value-returning conditions
- Use named arguments for boolean parameters to improve readability

## Dependency Injection

- Always use constructor injection — never use `$container->get()` or service locators
- Type-hint interfaces, not implementations: `LoggerInterface`, `CacheInterface`, `EntityManagerInterface`
- Use `#[Autowire]` for scalar or env parameters:
  ```php
  public function __construct(
      #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
  ) {}
  ```
- Use `#[AutowireIterator]` for tagged service collections
- Services should be private by default — expose only what's needed

## Controllers

- Keep controllers thin — maximum 10-15 lines per action method
- Delegate all business logic to services
- Use `#[Route]` attributes on methods, group with class-level prefix
- Use `#[MapEntity]` for automatic entity resolution from route parameters
- Use `#[MapRequestPayload]` for deserializing JSON request bodies into DTOs
- Use `#[MapQueryString]` for deserializing query parameters into DTOs
- Return `Response` objects — never echo or die
- Group related routes by resource and use RESTful naming

## Services & Architecture

- Follow single responsibility principle — one concern per class
- Use DTOs for moving data between layers
- Keep business logic in service classes, not in controllers or entities
- Use value objects for domain concepts (Money, Email, Address)
- Prefer composition over inheritance
- Name services descriptively: `InvoiceGenerator`, `UserRegistrationHandler`, not `UserService`

## Doctrine ORM

- Use PHP 8 attributes for all mapping — never XML or YAML
- Entities are plain PHP objects with no service dependencies
- Use `private` properties with getters, setters only when needed
- Never put `flush()` inside a repository — the caller controls the unit of work
- Use `UuidV7` or `Ulid` for public-facing IDs, auto-increment for internal
- Extend `ServiceEntityRepository` for custom repositories
- Use QueryBuilder for dynamic queries, DQL for static complex queries
- Always set `cascade` explicitly on relationships
- Use `fetch: 'EXTRA_LAZY'` for large collections
- Initialize collections in constructor: `$this->items = new ArrayCollection();`
- Always generate migrations with `doctrine:migrations:diff`

## Twig Templates

- Keep logic minimal — complex logic belongs in Twig extensions
- Use template inheritance properly with base, layout, and page templates
- Always escape output (default) — never use `|raw` unless absolutely necessary
- Use `{{ path() }}` for internal links, `{{ asset() }}` for static assets
- Use `|trans` filter for all user-visible text
- Use Twig Components (`#[AsTwigComponent]`) for reusable UI pieces

## Forms

- Create dedicated Form Type classes — never build forms inline
- Use proper form types: `EntityType`, `ChoiceType` with enums, `CollectionType`
- Validate in the Form Type or via entity constraints — never manually in controllers
- Use form events for dynamic forms, data transformers for type conversions

## Security

- Use Voters for all authorization decisions
- Never check roles directly in controllers — use `#[IsGranted]`
- Store secrets with `bin/console secrets:set` — never in `.env` for production
- Use CSRF protection on all forms
- Use `PasswordHasherInterface` — never hash passwords manually
- Use rate limiting on authentication endpoints

## Event System

- Use `#[AsEventListener]` attribute for listeners
- Prefer event listeners over subscribers for single-event handling
- For domain events, use Symfony Messenger as event bus
- Name events as past-tense facts: `UserRegistered`, `OrderPlaced`
- Keep event objects immutable

## Messenger (Async Processing)

- Use Messenger for deferrable operations: email, PDF, external API calls, heavy computations
- Messages are simple DTOs — no service dependencies, use readonly classes
- Carry only IDs and scalar data — not entities
- One handler per message
- Always handle failures: configure retry strategy and failure transport

## API Development

- Use serialization groups to control exposed fields
- Validate input with `#[MapRequestPayload]` and Symfony Validator constraints
- Use proper HTTP status codes: 201 creation, 204 deletion, 422 validation errors
- Version APIs via URL prefix (`/api/v1/`)
- Use pagination for list endpoints — never return unbounded collections

## Error Handling

- Use custom exception classes in `src/Exception/`
- Let Symfony's error handler convert exceptions to responses
- Log with context: `$logger->error('message', ['key' => $value])`
- Never expose internal errors to users

## Testing

- Write tests for all new features and bug fixes
- Use `WebTestCase` for functional tests, `KernelTestCase` for integration
- Mock external services, not internal ones
- Run with: `php bin/phpunit`

## Console Commands

- Use `#[AsCommand]` attribute with name and description
- Use `SymfonyStyle` for all output formatting
- Return `Command::SUCCESS` or `Command::FAILURE`
- Use progress bars for long-running operations
- Lock commands that shouldn't run concurrently with `LockFactory`

## Performance

- Use HTTP caching headers: `Cache-Control`, `ETag`, `Last-Modified`
- Enable OPcache in production
- Use Doctrine second-level cache for read-heavy entities
- Lazy-load services with `#[Lazy]` attribute
- Profile with Symfony Profiler and Blackfire

## File Structure

```
src/
  Command/           # Console commands
  Controller/        # HTTP controllers (thin)
  Entity/            # Doctrine entities
  EventListener/     # Event listeners
  Exception/         # Custom exceptions
  Form/              # Form types
  Message/           # Messenger messages
  MessageHandler/    # Messenger handlers
  Repository/        # Doctrine repositories
  Security/          # Voters, authenticators
  Service/           # Business logic services
  Twig/              # Twig extensions, components
  Validator/         # Custom validation constraints
config/              # Configuration files
templates/           # Twig templates
migrations/          # Doctrine migrations
tests/               # Tests (Unit/, Integration/, Functional/)
```
