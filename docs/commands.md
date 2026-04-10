# Console Commands

## boost:install

Interactive installation wizard for configuring AI agents.

```bash
bin/console boost:install
bin/console boost:install --guidelines
bin/console boost:install --skills
bin/console boost:install --mcp
bin/console boost:install --guidelines --skills --mcp
```

**Options:**
- `--guidelines` - Install AI coding guidelines
- `--skills` - Install agent skills
- `--mcp` - Install MCP server configuration

When run without options, the command interactively asks which features and agents to configure.

**What it does:**
1. Detects installed AI agents (system-wide and project-level)
2. Discovers third-party packages with `.ai/guidelines/` or `.ai/skills/`
3. Prompts for feature and agent selection
4. Writes guidelines, skills, and MCP config for each selected agent
5. Stores configuration in `boost.json` for future updates

## boost:update

Updates guidelines and skills for previously configured agents.

```bash
bin/console boost:update
```

Run this after:
- Updating `iadil/symfony-boost` to get new built-in guidelines/skills
- Installing new packages that ship `.ai/guidelines/` or `.ai/skills/`
- Modifying your project-level `.ai/guidelines/` or `.ai/skills/`

Reads the agent configuration from `boost.json` (created by `boost:install`).

## boost:add-skill

Install a remote skill from a GitHub repository.

```bash
bin/console boost:add-skill owner/repo
bin/console boost:add-skill https://github.com/owner/repo
bin/console boost:add-skill owner/repo --force
```

**Arguments:**
- `repository` (required) - GitHub repository in `owner/repo` format or full URL

**Options:**
- `--force`, `-f` - Overwrite existing skills

**What it does:**
1. Fetches the repository tree via GitHub API
2. Discovers skills (directories containing `SKILL.md`)
3. Runs a security audit
4. Downloads skill files
5. Installs to all configured agents' skill directories

Supports `GITHUB_TOKEN` environment variable for private repos or rate limit avoidance.

## boost:mcp

Starts the Symfony Boost MCP server (alias for `mcp:server`).

```bash
bin/console boost:mcp
```

This is a convenience wrapper around `mcp:server` provided by `symfony/mcp-bundle`. You can use either command.

## mcp:server

Starts the MCP server (provided by `symfony/mcp-bundle`).

```bash
bin/console mcp:server
```

This starts the STDIO transport, listening for JSON-RPC requests on stdin and responding on stdout. This is the command that AI agents call to communicate with the MCP server.
