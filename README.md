# Symfony Boost Bundle

Symfony Boost accelerates AI-assisted development by providing the essential context and structure that AI needs to generate high-quality, Symfony-specific code.

Built on top of `symfony/mcp-bundle`, this bundle provides an MCP (Model Context Protocol) server with tools that give AI assistants access to your application's database schema, logs, documentation, and more.

## Installation

```bash
composer require iadil/symfony-boost
```

## Features

### MCP Tools (9 tools)

- **application-info** - PHP/Symfony version, installed packages, Doctrine entities
- **browser-logs** - Read browser console logs captured from the frontend
- **database-connections** - List configured Doctrine DBAL connections
- **database-query** - Execute read-only SQL queries against the database
- **database-schema** - Full database schema introspection (tables, columns, indexes, foreign keys)
- **get-absolute-url** - Generate absolute URLs from routes or paths
- **last-error** - Get the last error from application logs
- **read-log-entries** - Read the last N log entries
- **search-docs** - Search Symfony ecosystem documentation

### MCP Prompts (4 prompts)

- **symfony-code-simplifier** - Simplify and refine PHP/Symfony code
- **upgrade-symfony-8** - Step-by-step Symfony 8 upgrade guide
- **upgrade-twig** - Twig template upgrade guide
- **upgrade-doctrine** - Doctrine ORM/DBAL upgrade guide

### MCP Resource

- **application-info** - Application info as a markdown resource

### Browser Log Capture

Automatically injects JavaScript into HTML responses to capture browser console logs and send them to `var/log/browser.log`.

### AI Agent Installation System

Configure AI development tools (Claude Code, Cursor, Copilot, Gemini, Junie, Codex, Amp, OpenCode) with:

- **Guidelines** - Project-specific AI coding guidelines
- **Skills** - Reusable AI skill definitions
- **MCP Configuration** - Per-agent MCP server configuration

## Configuration

```yaml
# config/packages/iadil_symfony_boost.yaml
iadil_symfony_boost:
    enabled: true
    browser_logs_watcher: true
    hosted:
        api_url: 'https://boost.laravel.com'
    mcp:
        tools:
            exclude: []
            include: []
        prompts:
            exclude: []
            include: []
```

## Console Commands

```bash
# Start the MCP server
bin/console boost:mcp

# Install Boost for AI agents
bin/console boost:install

# Update guidelines and skills
bin/console boost:update

# Add a remote skill from GitHub
bin/console boost:add-skill owner/repo
```

## License

MIT
