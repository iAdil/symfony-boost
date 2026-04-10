# Browser Log Capture

Symfony Boost can capture browser console output (logs, errors, warnings) and store them in your application's log directory, making frontend debugging available to AI agents via the `browser-logs` MCP tool.

## How It Works

1. An event listener (`InjectBrowserLoggerListener`) automatically injects a JavaScript snippet into HTML responses
2. The script intercepts `console.log`, `console.error`, `console.warn`, `console.info`, and `console.table`
3. Captured logs are batched and sent to `/_boost/browser-logs` via `fetch()` with a 100ms debounce
4. On page unload, remaining logs are sent via `navigator.sendBeacon()`
5. Logs are written to `var/log/browser.log` (or `var/log/dev.log` if browser channel is not configured)

## What Gets Captured

- `console.log()`, `console.info()`, `console.warn()`, `console.error()`, `console.table()`
- Uncaught errors (`window.onerror`)
- Window error events (`error` event)
- Unhandled Promise rejections (`unhandledrejection` event)

## Configuration

### Enable/Disable

```yaml
# config/packages/iadil_symfony_boost.yaml
iadil_symfony_boost:
    browser_logs_watcher: true   # true by default
```

### Routes

Import routes in `config/routes.yaml`:

```yaml
iadil_symfony_boost:
    resource: '@SymfonyBoostBundle/config/routes.yaml'
```

This registers the `POST /_boost/browser-logs` endpoint.

## Skipped Responses

The script is NOT injected for:
- JSON responses
- Redirects
- Binary file downloads
- Streamed responses
- Non-HTML content types
- Livewire navigation requests
- Responses that already contain the logger script

## CSP (Content Security Policy)

If you use a Content Security Policy, you may need to allow the inline script. The script tag includes an `id="browser-logger-active"` attribute. If you use `nelmio/security-bundle`, the script supports nonce-based CSP automatically.

## Reading Browser Logs

AI agents can read captured browser logs via the `browser-logs` MCP tool:

```
Tool: browser-logs
Parameters: { "entries": 20 }
```

This returns the last 20 browser log entries from `var/log/browser.log`.
