---
name: create-api-endpoint
description: Create a REST API endpoint with input validation, serialization groups, and proper HTTP responses
---
# Create API Endpoint

## Files to Create

1. `src/Controller/Api/{Name}Controller.php` — API controller
2. `src/Dto/{Name}Request.php` — Input DTO with validation
3. `src/Dto/{Name}Response.php` — Output DTO (optional)

## Controller Template

```php
<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\Create{Name}Request;
use App\Entity\{Name};
use App\Repository\{Name}Repository;
use App\Service\{Name}Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/{name}s')]
class {Name}Controller extends AbstractController
{
    public function __construct(
        private readonly {Name}Service $service,
        private readonly {Name}Repository $repository,
    ) {}

    #[Route('/', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $items = $this->repository->findAll();

        return $this->json($items, context: ['groups' => ['{name}:list']]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show({Name} ${name}): JsonResponse
    {
        return $this->json(${name}, context: ['groups' => ['{name}:detail']]);
    }

    #[Route('/', methods: ['POST'])]
    public function create(#[MapRequestPayload] Create{Name}Request $dto): JsonResponse
    {
        ${name} = $this->service->createFromDto($dto);

        return $this->json(${name}, Response::HTTP_CREATED, context: ['groups' => ['{name}:detail']]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update({Name} ${name}, #[MapRequestPayload] Create{Name}Request $dto): JsonResponse
    {
        $this->service->updateFromDto(${name}, $dto);

        return $this->json(${name}, context: ['groups' => ['{name}:detail']]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete({Name} ${name}): JsonResponse
    {
        $this->service->delete(${name});

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
```

## Input DTO Template

```php
<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Create{Name}Request
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $name,

        #[Assert\Length(max: 1000)]
        public ?string $description = null,
    ) {}
}
```

## Entity Serialization Groups

```php
// On entity properties:
#[Groups(['{name}:list', '{name}:detail'])]
private string $name;

#[Groups(['{name}:detail'])]
private string $description;
```

## HTTP Status Codes

- `200` — Successful GET/PUT
- `201` — Successful POST (resource created)
- `204` — Successful DELETE (no content)
- `400` — Bad request (malformed JSON)
- `404` — Resource not found
- `422` — Validation error

## Rules

- Always use DTOs for input — never bind directly to entities
- Use serialization groups to control output per endpoint
- Use `#[MapRequestPayload]` for automatic deserialization + validation
- Return proper HTTP status codes
- Use `readonly` DTOs
- Version API via URL prefix: `/api/v1/`
