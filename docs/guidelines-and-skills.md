# Guidelines & Skills

## Guidelines

Guidelines are coding standards and best practices written to agent instruction files (e.g., `CLAUDE.md`). They tell the AI agent how to write code for your project.

### Sources (in order of precedence)

1. **Built-in** - Comprehensive Symfony guidelines shipped with the bundle
2. **Composer packages** - Any package with a `.ai/guidelines/` directory
3. **NPM packages** - Any package with a `.ai/guidelines/` directory
4. **Project-level** - Your own `.ai/guidelines/` directory in the project root

### Built-in Guidelines

The bundle ships with comprehensive Symfony guidelines covering:
- PHP standards and modern features
- Dependency injection best practices
- Controller patterns
- Doctrine ORM conventions
- Twig template standards
- Form types and validation
- Security and authorization
- Event system and Messenger
- API development
- Error handling
- Performance optimization
- Console commands
- File structure

### Custom Project Guidelines

Create `.ai/guidelines/` in your project root with `.md` files:

```
.ai/
  guidelines/
    coding-standards.md
    api-conventions.md
    deployment-rules.md
```

These are merged with built-in guidelines when running `boost:install`.

### Third-Party Package Guidelines

Package authors can ship guidelines by including `.ai/guidelines/` in their package:

```
vendor/acme/my-bundle/
  .ai/
    guidelines/
      usage.md
```

## Skills

Skills are reusable instruction files that AI agents can reference for specific tasks. They're installed to the agent's skills directory (e.g., `.claude/skills/`).

### Built-in Skills (10)

| Skill | Description |
|-------|-------------|
| `create-entity` | Doctrine entity with relationships, lifecycle callbacks, repository |
| `create-crud` | Complete CRUD: controller, service, form type, templates |
| `create-api-endpoint` | REST API with DTOs, validation, serialization groups |
| `create-command` | Console command with progress bars, locking, signal handling |
| `create-event-system` | Custom events, listeners, async processing via Messenger |
| `create-voter` | Security voter for fine-grained authorization |
| `create-form` | Complex forms with data transformers and form events |
| `create-test` | Unit, integration, and functional tests |
| `debug-performance` | Performance profiling, N+1 detection, caching |
| `database-migration` | Safe migration patterns for zero-downtime deployments |

### Skill Format

Each skill is a directory containing a `SKILL.md` file with YAML frontmatter:

```
my-skill/
  SKILL.md
  references/
    example.md
    template.php
```

```markdown
---
name: my-skill
description: What this skill does
---
# Instructions for the AI agent

Step-by-step guide with code examples...
```

### Custom Skills

Create `.ai/skills/` in your project root:

```
.ai/
  skills/
    deploy-checklist/
      SKILL.md
    code-review/
      SKILL.md
```

### Remote Skills

Install skills from GitHub repositories:

```bash
bin/console boost:add-skill owner/repo
```

This downloads the skill files and installs them for all configured agents. A security audit is performed before installation.

### Third-Party Package Skills

Package authors can ship skills by including `.ai/skills/` in their package:

```
vendor/acme/my-bundle/
  .ai/
    skills/
      setup-acme/
        SKILL.md
```
