# Symfony Boost Bundle

Symfony Boost accelerates AI-assisted development by providing the essential context and structure that AI needs to generate high-quality, Symfony-specific code.

Built on top of [`symfony/mcp-bundle`](https://symfony.com/doc/current/ai/bundles/mcp-bundle.html), this bundle provides an MCP (Model Context Protocol) server with 15 tools that give AI assistants access to your application's database schema, logs, routes, configuration, documentation, and more.

## Documentation

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [MCP Tools](docs/tools.md)
- [MCP Prompts & Resources](docs/prompts-and-resources.md)
- [Browser Log Capture](docs/browser-logs.md)
- [AI Agent Setup](docs/agent-setup.md)
- [Guidelines & Skills](docs/guidelines-and-skills.md)
- [Console Commands](docs/commands.md)

## Quick Start

```bash
composer require iadil/symfony-boost
```

Register the bundle (if Symfony Flex didn't auto-register):

```php
// config/bundles.php
return [
    // ...
    IAdil\SymfonyBoostBundle\SymfonyBoostBundle::class => ['all' => true],
];
```

Create `config/packages/mcp.yaml`:

```yaml
mcp:
    app: 'my-app'
    version: '1.0.0'
    client_transports:
        stdio: true
    discovery:
        scan_dirs:
            - src
            - vendor/iadil/symfony-boost/src
```

Install guidelines and skills for your AI agent:

```bash
bin/console boost:install
```

Start the MCP server:

```bash
bin/console mcp:server
```

## Features

### 15 MCP Tools

| Tool | Description |
|------|-------------|
| `application-info` | PHP/Symfony version, installed packages, Doctrine entities |
| `browser-logs` | Read browser console logs captured from the frontend |
| `database-connections` | List configured Doctrine DBAL connections |
| `database-query` | Execute read-only SQL queries against the database |
| `database-schema` | Full database schema introspection |
| `get-absolute-url` | Generate absolute URLs from routes or paths |
| `get-config-value` | Read Symfony parameters by key |
| `last-error` | Get the last error from application logs |
| `list-bundles` | List all registered Symfony bundles |
| `list-env-vars` | List application environment variables |
| `list-routes` | List all registered routes with controllers |
| `read-log-entries` | Read the last N log entries |
| `run-console` | Execute read-only Symfony console commands |
| `search-docs` | Search Symfony ecosystem documentation |
| `table-row-count` | Row counts for all database tables |

### 4 MCP Prompts

| Prompt | Description |
|--------|-------------|
| `symfony-code-simplifier` | Simplify and refine PHP/Symfony code |
| `upgrade-symfony-8` | Step-by-step Symfony 8 upgrade guide |
| `upgrade-twig` | Twig template upgrade guide |
| `upgrade-doctrine` | Doctrine ORM/DBAL upgrade guide |

### 10 Built-in Skills

| Skill | Description |
|-------|-------------|
| `create-entity` | Doctrine entity with relationships and lifecycle callbacks |
| `create-crud` | Complete CRUD: controller, service, form, templates |
| `create-api-endpoint` | REST API with validation and serialization |
| `create-command` | Console command with progress, locking, signals |
| `create-event-system` | Events, listeners, and async Messenger processing |
| `create-voter` | Security voter for authorization |
| `create-form` | Complex forms with transformers and events |
| `create-test` | Unit, integration, and functional tests |
| `debug-performance` | Performance profiling and optimization |
| `database-migration` | Safe Doctrine migration patterns |

### Supported AI Agents

Claude Code, Cursor, GitHub Copilot, Gemini, Junie, Codex, Amp, OpenCode

## License

MIT
