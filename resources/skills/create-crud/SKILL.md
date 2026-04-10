---
name: create-crud
description: Create a complete CRUD with controller, service, form type, and Twig templates
---
# Create CRUD

## Files to Create

1. `src/Controller/{Name}Controller.php` — Thin controller with routes
2. `src/Service/{Name}Service.php` — Business logic
3. `src/Form/{Name}Type.php` — Form type
4. `templates/{name}/index.html.twig` — List view
5. `templates/{name}/show.html.twig` — Detail view
6. `templates/{name}/new.html.twig` — Create form
7. `templates/{name}/edit.html.twig` — Edit form
8. `templates/{name}/_form.html.twig` — Shared form partial
9. `templates/{name}/_delete_form.html.twig` — Delete confirmation

## Controller Template

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\{Name};
use App\Form\{Name}Type;
use App\Service\{Name}Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{name}s')]
class {Name}Controller extends AbstractController
{
    public function __construct(
        private readonly {Name}Service ${name}Service,
    ) {}

    #[Route('/', name: '{name}_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        ${name}s = $this->{name}Service->getPaginated($page);

        return $this->render('{name}/index.html.twig', [
            '{name}s' => ${name}s,
        ]);
    }

    #[Route('/new', name: '{name}_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        ${name} = new {Name}();
        $form = $this->createForm({Name}Type::class, ${name});
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->{name}Service->create(${name});
            $this->addFlash('success', '{Name} created.');

            return $this->redirectToRoute('{name}_index');
        }

        return $this->render('{name}/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '{name}_show', methods: ['GET'])]
    public function show({Name} ${name}): Response
    {
        return $this->render('{name}/show.html.twig', [
            '{name}' => ${name},
        ]);
    }

    #[Route('/{id}/edit', name: '{name}_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, {Name} ${name}): Response
    {
        $form = $this->createForm({Name}Type::class, ${name});
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->{name}Service->update(${name});
            $this->addFlash('success', '{Name} updated.');

            return $this->redirectToRoute('{name}_show', ['id' => ${name}->getId()]);
        }

        return $this->render('{name}/edit.html.twig', [
            '{name}' => ${name},
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '{name}_delete', methods: ['POST'])]
    public function delete(Request $request, {Name} ${name}): Response
    {
        if ($this->isCsrfTokenValid('delete'.${name}->getId(), $request->getPayload()->getString('_token'))) {
            $this->{name}Service->delete(${name});
            $this->addFlash('success', '{Name} deleted.');
        }

        return $this->redirectToRoute('{name}_index');
    }
}
```

## Service Template

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{Name};
use App\Repository\{Name}Repository;
use Doctrine\ORM\EntityManagerInterface;

final class {Name}Service
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly {Name}Repository $repository,
    ) {}

    public function getPaginated(int $page, int $limit = 20): array
    {
        return $this->repository->findBy([], ['createdAt' => 'DESC'], $limit, ($page - 1) * $limit);
    }

    public function create({Name} ${name}): void
    {
        $this->em->persist(${name});
        $this->em->flush();
    }

    public function update({Name} ${name}): void
    {
        $this->em->flush();
    }

    public function delete({Name} ${name}): void
    {
        $this->em->remove(${name});
        $this->em->flush();
    }
}
```

## Form Type Template

```php
<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\{Name};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {Name}Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            // Add more fields based on entity properties
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {Name}::class,
        ]);
    }
}
```

## Rules

- Controller is thin — all logic in the service
- Always use CSRF protection on delete actions
- Use flash messages for user feedback
- Service handles persistence — controller never calls `flush()`
- Form type is a separate class, never inline
- Use `{Name}` as the entity, `{name}` as the variable/route prefix
