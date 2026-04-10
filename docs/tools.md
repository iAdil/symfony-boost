# MCP Tools

Symfony Boost provides 15 MCP tools that give AI assistants deep context about your application.

## application-info

Returns comprehensive application information.

**Output:** PHP version, Symfony version, database engine, all installed Composer packages with versions, all Doctrine entities.

**Usage:** AI agents should call this tool at the start of each chat to understand the project's tech stack and write version-specific code.

## browser-logs

Read the last N entries from the browser console log.

**Parameters:**
- `entries` (int, required) - Number of log entries to return

**Requires:** Browser log capture enabled (`browser_logs_watcher: true`)

## database-connections

List all configured Doctrine DBAL connection names.

**Output:** Default connection name and list of all connection names.

## database-query

Execute a read-only SQL query against the database.

**Parameters:**
- `query` (string, required) - SQL query (SELECT, SHOW, EXPLAIN, DESCRIBE only)
- `database` (string, optional) - Connection name

**Safety:** Only read-only queries are allowed. INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE are blocked. WITH...SELECT (CTEs) are supported.

## database-schema

Read the database schema for the application.

**Parameters:**
- `summary` (bool) - Return only table names and column types (use first for overview)
- `database` (string) - Connection name
- `filter` (string) - Filter tables by name (substring match)
- `include_views` (bool) - Include database views
- `include_routines` (bool) - Include stored procedures, functions, sequences
- `include_column_details` (bool) - Include nullable, default, auto_increment, comments

**Recommended usage:** Call with `summary: true` first, then with `filter` for specific tables.

## get-absolute-url

Generate an absolute URL from a relative path or named route.

**Parameters:**
- `path` (string) - Relative URL path (e.g., `/dashboard`)
- `route` (string) - Named route (e.g., `app_dashboard`)

## get-config-value

Read a Symfony parameter value by key.

**Parameters:**
- `key` (string, required) - Parameter name (e.g., `kernel.project_dir`, `kernel.environment`)

## last-error

Get the last ERROR entry from application logs. Checks cache first, then falls back to reading the log file.

## list-bundles

List all registered Symfony bundles with their class names and paths.

## list-env-vars

List application environment variables. Sensitive values (passwords, tokens, DSNs) are automatically masked.

**Parameters:**
- `filter` (string) - Filter by variable name (substring match)

## list-routes

List all registered routes with name, path, HTTP methods, and controller.

**Parameters:**
- `filter` (string) - Filter by route name or path (substring match)

## read-log-entries

Read the last N log entries from the application log file. Handles both PSR-3 formatted and JSON-formatted logs, correctly parsing multi-line entries.

**Parameters:**
- `entries` (int, required) - Number of log entries to return

## run-console

Execute a read-only Symfony console command.

**Parameters:**
- `command` (string, required) - Command name (e.g., `debug:router`)
- `arguments` (string, optional) - Command arguments

**Allowed commands:** `debug:router`, `debug:container`, `debug:config`, `debug:autowiring`, `debug:event-dispatcher`, `debug:translation`, `debug:twig`, `debug:validator`, `debug:form`, `debug:messenger`, `doctrine:schema:validate`, `doctrine:mapping:info`, `doctrine:migrations:status`, `doctrine:migrations:list`, `lint:twig`, `lint:yaml`, `lint:container`, `router:match`, `about`, `list`, `cache:pool:list`, `secrets:list`, and any `debug:*` or `lint:*` command.

**Blocked commands:** Any command that could modify state (cache:clear, doctrine:migrations:migrate, etc.)

## search-docs

Search version-specific Symfony ecosystem documentation.

**Parameters:**
- `queries` (array of strings, required) - Search queries
- `packages` (array of strings, optional) - Limit search to specific packages
- `token_limit` (int) - Maximum tokens to return (default: 3000, max: 1,000,000)

## table-row-count

Get row counts for all database tables.

**Parameters:**
- `database` (string) - Connection name
- `filter` (string) - Filter tables by name

**Output:** Table names with row counts, sorted by count descending.
