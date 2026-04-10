# Configuration

## Symfony Boost Configuration

Create `config/packages/iadil_symfony_boost.yaml`:

```yaml
iadil_symfony_boost:
    # Master switch to enable/disable all Boost functionality
    enabled: true

    # Enable browser console log capture
    browser_logs_watcher: true

    # Custom executable paths (leave null for auto-detection)
    executable_paths:
        php: null
        composer: null
        npm: null
        vendor_bin: null
        current_directory: null

    # Hosted documentation search API
    hosted:
        api_url: 'https://boost.laravel.com'

    # Control which MCP capabilities are registered
    mcp:
        tools:
            exclude: []    # Class names to exclude
            include: []    # Extra class names to include
        resources:
            exclude: []
            include: []
        prompts:
            exclude: []
            include: []
```

## MCP Bundle Configuration

Create `config/packages/mcp.yaml`:

```yaml
mcp:
    # Application identity
    app: 'my-app'
    version: '1.0.0'
    description: 'My application MCP server'
    instructions: 'Provides database schema, logs, docs search and more.'

    # Enable transports
    client_transports:
        stdio: true      # For CLI-based clients (Claude Code, etc.)
        http: true       # For web-based clients (MCP Inspector)

    # Tool/prompt/resource discovery
    discovery:
        scan_dirs:
            - src
            - vendor/iadil/symfony-boost/src

    # HTTP transport settings (if enabled)
    http:
        path: /_mcp
        session:
            store: file
            ttl: 3600
```

## Disabling Specific Tools

To disable a tool, add its class name to the exclude list:

```yaml
iadil_symfony_boost:
    mcp:
        tools:
            exclude:
                - IAdil\SymfonyBoostBundle\Mcp\Tool\DatabaseQueryTool
                - IAdil\SymfonyBoostBundle\Mcp\Tool\RunConsoleTool
```

## Disabling Browser Log Capture

```yaml
iadil_symfony_boost:
    browser_logs_watcher: false
```

This disables:
- JavaScript injection into HTML responses
- The `/_boost/browser-logs` endpoint
- The `browser-logs` MCP tool will still work if you have existing log files

## Environment Variables

You can override configuration with environment variables:

```env
# .env or .env.local
BOOST_ENABLED=true
BOOST_BROWSER_LOGS_WATCHER=true
```
