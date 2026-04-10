# AI Agent Setup

Symfony Boost detects and configures multiple AI development agents. Each agent gets its own guidelines file, skills directory, and MCP server configuration.

## Supported Agents

| Agent | Guidelines File | Skills Directory | MCP Config |
|-------|----------------|-----------------|------------|
| Claude Code | `CLAUDE.md` | `.claude/skills/` | `.mcp.json` |
| Cursor | `AGENTS.md` | `.cursor/skills/` | `.cursor/mcp.json` |
| GitHub Copilot | `.github/copilot-instructions.md` | `.github/skills/` | `.vscode/mcp.json` |
| Amp | `AGENTS.md` | `.amp/skills/` | `.amp/mcp.json` |
| Codex | `AGENTS.md` | `.codex/skills/` | `codex.toml` |
| Gemini | `GEMINI.md` | `.gemini/skills/` | `.gemini/settings.json` |
| Junie | `.junie/guidelines.md` | `.junie/skills/` | `.junie/mcp.json` |
| OpenCode | `AGENTS.md` | `.opencode/skills/` | `opencode.json` |

## Installation

### Interactive

```bash
bin/console boost:install
```

This will:
1. Detect agents installed on your system and in the project
2. Ask which features to install (guidelines, skills, MCP config)
3. Ask which agents to configure
4. Write files for each selected agent

### Non-Interactive

Use flags to skip prompts:

```bash
bin/console boost:install --guidelines --skills --mcp
```

## Updating

After updating `iadil/symfony-boost` or adding new packages with `.ai/guidelines/` or `.ai/skills/`:

```bash
bin/console boost:update
```

This re-generates guidelines and skills for all previously configured agents.

## Agent Detection

Boost detects agents two ways:

**System-wide:** Checks if the agent binary is installed (e.g., `command -v claude`, checking `/Applications/Cursor.app`)

**Project-level:** Checks for agent configuration files in the project (e.g., `.claude/` directory, `CLAUDE.md` file)

## MCP Server Connection

### Local (STDIO)

For agents running on the same machine, the MCP config points to the Symfony console command:

```json
{
    "mcpServers": {
        "symfony-boost": {
            "command": "php",
            "args": ["bin/console", "mcp:server"]
        }
    }
}
```

### Docker

For projects running in Docker:

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

### HTTP Transport

Some agents (like Cursor) can connect via HTTP. Enable HTTP transport in `config/packages/mcp.yaml`:

```yaml
mcp:
    client_transports:
        http: true
    http:
        path: /_mcp
```

Then configure the agent to use HTTP MCP:

```json
{
    "mcpServers": {
        "symfony-boost": {
            "command": "npx",
            "args": ["-y", "mcp-remote", "http://localhost:8000/_mcp"]
        }
    }
}
```
