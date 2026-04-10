---
name: create-event-system
description: Create custom events, listeners, and async processing via Messenger
---
# Create Event System

## Files to Create

1. `src/Event/{Name}Event.php` — Event class
2. `src/EventListener/{Name}Listener.php` — Listener class
3. Optionally: `src/Message/{Name}Message.php` + `src/MessageHandler/{Name}MessageHandler.php` for async

## Event Template

```php
<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class UserRegisteredEvent extends Event
{
    public function __construct(
        public readonly int $userId,
        public readonly string $email,
        public readonly \DateTimeImmutable $registeredAt = new \DateTimeImmutable(),
    ) {}
}
```

## Listener Template

```php
<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class SendWelcomeEmailListener
{
    public function __invoke(UserRegisteredEvent $event): void
    {
        // Send welcome email to $event->email
    }
}
```

## Multiple Events on One Listener

```php
#[AsEventListener(event: UserRegisteredEvent::class, method: 'onRegistered')]
#[AsEventListener(event: UserVerifiedEvent::class, method: 'onVerified')]
final class UserNotificationListener
{
    public function onRegistered(UserRegisteredEvent $event): void { }
    public function onVerified(UserVerifiedEvent $event): void { }
}
```

## Dispatching Events

```php
// In a service:
$this->eventDispatcher->dispatch(new UserRegisteredEvent(
    userId: $user->getId(),
    email: $user->getEmail(),
));
```

## Async via Messenger

For heavy operations, use Messenger instead of synchronous listeners:

```php
// src/Message/SendWelcomeEmail.php
final readonly class SendWelcomeEmail
{
    public function __construct(
        public int $userId,
    ) {}
}

// src/MessageHandler/SendWelcomeEmailHandler.php
#[AsMessageHandler]
final class SendWelcomeEmailHandler
{
    public function __invoke(SendWelcomeEmail $message): void
    {
        // Heavy work here — runs async
    }
}

// config/packages/messenger.yaml
framework:
    messenger:
        routing:
            App\Message\SendWelcomeEmail: async
```

## Rules

- Name events as past-tense facts: `OrderPlaced`, `UserRegistered`
- Keep events immutable — use readonly properties
- Carry only IDs and scalar data, not entities
- Use `#[AsEventListener]` attribute, not YAML config
- Use Messenger for anything that can be deferred (email, external API)
- One handler per message
