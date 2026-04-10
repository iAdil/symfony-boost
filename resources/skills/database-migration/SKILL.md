---
name: database-migration
description: Create and manage safe Doctrine database migrations
---
# Database Migration

## Workflow

1. Modify entity mapping (PHP attributes)
2. Generate migration: `bin/console doctrine:migrations:diff`
3. Review the generated SQL
4. Run migration: `bin/console doctrine:migrations:migrate`

## Common Migration Patterns

### Add a column (nullable first for zero-downtime)

```php
public function up(Schema $schema): void
{
    // Step 1: Add nullable column
    $this->addSql('ALTER TABLE users ADD phone VARCHAR(20) DEFAULT NULL');
}

// In a second migration after backfilling:
public function up(Schema $schema): void
{
    // Step 2: Make non-nullable after data is populated
    $this->addSql('ALTER TABLE users ALTER phone SET NOT NULL');
}
```

### Add an index

```php
public function up(Schema $schema): void
{
    $this->addSql('CREATE INDEX idx_users_email ON users (email)');
}

public function down(Schema $schema): void
{
    $this->addSql('DROP INDEX idx_users_email');
}
```

### Rename a column

```php
public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE users RENAME COLUMN name TO full_name');
}
```

### Add a foreign key

```php
public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE posts ADD author_id INT NOT NULL');
    $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_AUTHOR FOREIGN KEY (author_id) REFERENCES users (id)');
    $this->addSql('CREATE INDEX IDX_AUTHOR ON posts (author_id)');
}
```

### Data migration

```php
public function up(Schema $schema): void
{
    // Schema change
    $this->addSql('ALTER TABLE users ADD role VARCHAR(20) DEFAULT \'user\' NOT NULL');

    // Data backfill
    $this->addSql("UPDATE users SET role = 'admin' WHERE is_admin = true");

    // Remove old column
    $this->addSql('ALTER TABLE users DROP is_admin');
}
```

## Commands

```bash
# Generate migration from entity diff
bin/console doctrine:migrations:diff

# Run pending migrations
bin/console doctrine:migrations:migrate

# Check migration status
bin/console doctrine:migrations:status

# List all migrations
bin/console doctrine:migrations:list

# Rollback last migration
bin/console doctrine:migrations:migrate prev

# Execute a specific migration
bin/console doctrine:migrations:execute 'DoctrineMigrations\Version20240101000000' --up

# Validate schema matches entities
bin/console doctrine:schema:validate
```

## Rules

- Never edit entities and migrations in the same commit
- Always review generated SQL before running
- Always implement `down()` method for rollbacks
- For zero-downtime deployments:
  1. Add nullable column first
  2. Deploy code that writes to new column
  3. Backfill existing data
  4. Make column non-nullable
- Never drop columns in production without a deprecation period
- Test migrations on a database copy first
- Use `$this->addSql()` — never use Doctrine DBAL in migrations
