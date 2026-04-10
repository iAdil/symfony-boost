---
name: create-entity
description: Create a Doctrine entity with proper mapping, relationships, lifecycle callbacks, and repository
---
# Create Doctrine Entity

## Steps

1. Create the entity class in `src/Entity/` with PHP 8 attribute mapping
2. Create the repository in `src/Repository/` extending `ServiceEntityRepository`
3. Generate a migration with `bin/console doctrine:migrations:diff`

## Entity Template

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\{Name}Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: {Name}Repository::class)]
#[ORM\Table(name: '{table_name}')]
#[ORM\HasLifecycleCallbacks]
class {Name}
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $name = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and setters...
}
```

## Relationship Patterns

### ManyToOne (child side)
```php
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
#[ORM\JoinColumn(nullable: false)]
private Category $category;
```

### OneToMany (parent side)
```php
#[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
private Collection $products;

// In constructor:
$this->products = new ArrayCollection();
```

### ManyToMany
```php
#[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
#[ORM\JoinTable(name: 'post_tag')]
private Collection $tags;
```

## Repository Template

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\{Name};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class {Name}Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, {Name}::class);
    }

    /** @return {Name}[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

## Rules

- Always use typed properties with proper defaults
- Use constructor for required fields and collection initialization
- Use `#[Assert\*]` constraints for validation
- Use lifecycle callbacks for `createdAt`/`updatedAt` timestamps
- Never inject services into entities
- Use `readonly` where appropriate
- After creating entity and repository, run: `bin/console doctrine:migrations:diff`
