# MCP Prompts & Resources

## Prompts

MCP prompts provide system instructions that AI agents can use for specific tasks.

### symfony-code-simplifier

A comprehensive prompt for simplifying and refining PHP/Symfony code. Focuses on:
- Preserving exact functionality while improving clarity
- Applying Symfony coding standards and best practices
- Reducing complexity and removing redundant code
- Using parallel background agents for multi-file refinement

### upgrade-symfony-8

Step-by-step guide for upgrading from Symfony 7.x to 8.0. Covers:
- Deprecation review and fix process
- High-priority breaking changes (annotations to attributes, removed methods)
- Medium-priority changes (security config, Messenger, forms)
- Verification commands after upgrade

### upgrade-twig

Guide for upgrading Twig templates. Covers:
- Deprecated tags and filters (`{% spaceless %}` to `{% apply spaceless %}`, etc.)
- Template naming convention changes
- New features to adopt (arrow functions, null coalescing, enum support)

### upgrade-doctrine

Guide for upgrading Doctrine ORM and DBAL. Covers:
- DBAL method renames (`fetchAll` to `fetchAllAssociative`, etc.)
- ORM changes (annotation to attribute migration, flush behavior, proxy changes)
- Migration patterns and verification steps

## Resources

### application-info

**URI:** `file://instructions/application-info.md`
**MIME Type:** `text/markdown`

Provides a markdown-formatted overview of the application including PHP version, Symfony version, installed packages, and Doctrine entities. AI agents can read this resource for persistent context about the project.
