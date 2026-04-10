---
name: debug-performance
description: Debug and optimize Symfony application performance
---
# Debug Performance

## Symfony Profiler

The Symfony Profiler (web debug toolbar) shows:
- Request/response time
- Database queries (count, time, duplicates)
- Memory usage
- Cache hits/misses
- Event listeners executed
- Twig templates rendered

Access at: `/_profiler/` for recent requests.

## Common Performance Issues

### 1. N+1 Query Problem

**Detect**: Check profiler for high query count on list pages.

**Fix**: Use eager loading or JOIN queries:
```php
// Bad — N+1 queries
$posts = $repo->findAll();
foreach ($posts as $post) {
    echo $post->getAuthor()->getName(); // Extra query per post
}

// Good — JOIN fetch
$posts = $repo->createQueryBuilder('p')
    ->leftJoin('p.author', 'a')
    ->addSelect('a')
    ->getQuery()
    ->getResult();
```

### 2. Missing Database Indexes

**Detect**: Slow queries in profiler.

**Fix**: Add indexes to frequently queried columns:
```php
#[ORM\Entity]
#[ORM\Index(columns: ['email'], name: 'idx_email')]
#[ORM\Index(columns: ['status', 'created_at'], name: 'idx_status_date')]
class User { }
```

### 3. Uncached Expensive Operations

**Fix**: Use Symfony Cache:
```php
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

$result = $this->cache->get('expensive_key', function (ItemInterface $item): array {
    $item->expiresAfter(3600);

    return $this->computeExpensiveResult();
});
```

### 4. Heavy Twig Templates

**Fix**:
- Use `{% cache %}` tag for expensive template fragments
- Avoid complex logic in templates — move to Twig extensions
- Use lazy-loading for below-the-fold content

### 5. Unoptimized Doctrine Hydration

**Fix**: Use array hydration for read-only lists:
```php
$results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
```

## Console Commands for Debugging

```bash
# Show all routes
bin/console debug:router

# Show all services
bin/console debug:container --show-arguments

# Show all event listeners
bin/console debug:event-dispatcher

# Validate Doctrine schema
bin/console doctrine:schema:validate

# Show Doctrine mapping info
bin/console doctrine:mapping:info

# Show all Messenger routes
bin/console debug:messenger
```

## Production Checklist

- `APP_ENV=prod` and `APP_DEBUG=0`
- OPcache enabled with proper settings
- `composer install --no-dev --optimize-autoloader`
- `bin/console cache:clear --env=prod`
- `bin/console assets:install`
- Database indexes on all frequently queried columns
- HTTP cache headers configured
- Doctrine proxy classes pre-generated
