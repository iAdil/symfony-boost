# Installation

## Requirements

- PHP 8.2+
- Symfony 7.0+ or 8.0+
- Doctrine DBAL 3.8+ or 4.0+
- Doctrine ORM 2.17+ or 3.0+

## Step 1: Install the Package

```bash
composer require iadil/symfony-boost
```

## Step 2: Register the Bundle

If Symfony Flex didn't auto-register, add it to `config/bundles.php`:

```php
return [
    // ...
    IAdil\SymfonyBoostBundle\SymfonyBoostBundle::class => ['all' => true],
];
```

## Step 3: Configure the MCP Bundle

The MCP server is powered by `symfony/mcp-bundle`. Create `config/packages/mcp.yaml`:

```yaml
mcp:
    app: 'my-app'
    version: '1.0.0'
    description: 'My application MCP server'
    instructions: 'Symfony application MCP server providing database schema, logs, docs search and more.'
    client_transports:
        stdio: true
        http: true       # Optional: enable for web-based MCP clients
    discovery:
        scan_dirs:
            - src
            - vendor/iadil/symfony-boost/src
```

The `discovery.scan_dirs` must include `vendor/iadil/symfony-boost/src` so the MCP bundle can discover the tools, prompts, and resources provided by Symfony Boost.

## Step 4: Configure Symfony Boost (Optional)

Create `config/packages/iadil_symfony_boost.yaml`:

```yaml
iadil_symfony_boost:
    enabled: true
    browser_logs_watcher: true
```

See [Configuration](configuration.md) for all options.

## Step 5: Import Routes (Optional)

If you want the browser log capture feature, import the routes in `config/routes.yaml`:

```yaml
iadil_symfony_boost:
    resource: '@SymfonyBoostBundle/config/routes.yaml'
```

## Step 6: Install AI Agent Configuration

Run the install command to set up guidelines, skills, and MCP config for your AI agents:

```bash
bin/console boost:install
```

This will:
- Detect which AI agents you have installed (Claude Code, Cursor, etc.)
- Write coding guidelines to the appropriate file (e.g., `CLAUDE.md`)
- Install skills to the agent's skills directory (e.g., `.claude/skills/`)
- Configure MCP server connection in the agent's config

## Step 7: Verify

```bash
# Check services are registered
bin/console debug:container | grep iadil

# Test the MCP server
bin/console mcp:server
```

## Docker Setup

If your Symfony app runs in Docker, you need to run commands inside the container:

```bash
docker compose exec php composer require iadil/symfony-boost
docker compose exec php bin/console boost:install
docker compose exec php bin/console mcp:server
```

For AI agents to connect to the MCP server inside Docker, configure the MCP server command in your agent's config to exec into the container:

```json
{
    "mcpServers": {
        "symfony-boost": {
            "command": "docker",
            "args": ["compose", "exec", "-T", "php", "php", "bin/console", "mcp:server"]
        }
    }
}
```

## Troubleshooting

### "There are no commands defined in the mcp namespace"

Make sure `symfony/mcp-bundle` is installed and registered:

```bash
composer show symfony/mcp-bundle
```

Check `config/bundles.php` includes:
```php
Symfony\AI\McpBundle\McpBundle::class => ['all' => true],
```

### "No tools found" in MCP server

Ensure `vendor/iadil/symfony-boost/src` is in the MCP discovery scan dirs:

```yaml
# config/packages/mcp.yaml
mcp:
    discovery:
        scan_dirs:
            - src
            - vendor/iadil/symfony-boost/src
```

### "Cannot autowire service" errors

Run `bin/console cache:clear` after installation. If issues persist, check that Doctrine ORM is properly configured in your project.
