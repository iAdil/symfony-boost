---
name: create-voter
description: Create a Symfony security voter for fine-grained authorization
---
# Create Security Voter

## Template

```php
<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PostVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Post $post */
        $post = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($post, $user),
            self::EDIT => $this->canEdit($post, $user),
            self::DELETE => $this->canDelete($post, $user),
            default => false,
        };
    }

    private function canView(Post $post, User $user): bool
    {
        // Published posts are visible to everyone
        if ($post->isPublished()) {
            return true;
        }

        // Drafts only visible to author
        return $post->getAuthor() === $user;
    }

    private function canEdit(Post $post, User $user): bool
    {
        return $post->getAuthor() === $user;
    }

    private function canDelete(Post $post, User $user): bool
    {
        return $post->getAuthor() === $user;
    }
}
```

## Usage in Controllers

```php
// Attribute-based (preferred)
#[IsGranted('EDIT', subject: 'post')]
public function edit(Post $post): Response { }

// Programmatic check
if (!$this->isGranted('EDIT', $post)) {
    throw $this->createAccessDeniedException();
}
```

## Usage in Twig

```twig
{% if is_granted('EDIT', post) %}
    <a href="{{ path('post_edit', {id: post.id}) }}">Edit</a>
{% endif %}
```

## Rules

- Use constants for attribute names
- Use `match` expression for clean attribute routing
- Always check `$user instanceof User` — anonymous users return null
- Keep permission logic in the voter, not scattered across controllers
- Register automatically via autoconfigure (no manual config needed)
- Use `#[IsGranted]` attribute on controller methods
