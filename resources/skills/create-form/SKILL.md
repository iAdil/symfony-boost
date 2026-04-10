---
name: create-form
description: Create complex Symfony form types with data transformers, events, and custom validation
---
# Create Complex Form Type

## Basic Form Type

```php
<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Draft' => 'draft',
                    'Published' => 'published',
                    'Archived' => 'archived',
                ],
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TagType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
```

## Dynamic Form with Events

```php
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder->add('country', EntityType::class, [
        'class' => Country::class,
        'placeholder' => 'Select country',
    ]);

    // Add city field dynamically based on selected country
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
        $data = $event->getData();
        $form = $event->getForm();
        $country = $data?->getCountry();

        $form->add('city', EntityType::class, [
            'class' => City::class,
            'choices' => $country ? $country->getCities() : [],
            'placeholder' => 'Select city',
        ]);
    });

    $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
        $data = $event->getData();
        $form = $event->getForm();
        $countryId = $data['country'] ?? null;

        // Rebuild city choices based on submitted country
        $form->add('city', EntityType::class, [
            'class' => City::class,
            'query_builder' => fn ($repo) => $repo->createQueryBuilder('c')
                ->where('c.country = :country')
                ->setParameter('country', $countryId),
        ]);
    });
}
```

## Data Transformer

```php
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    // Entity Collection -> comma-separated string
    public function transform(mixed $value): string
    {
        if (null === $value) return '';

        return implode(', ', $value->map(fn ($t) => $t->getName())->toArray());
    }

    // Comma-separated string -> Entity Collection
    public function reverseTransform(mixed $value): array
    {
        if (!$value) return [];

        return array_map('trim', explode(',', $value));
    }
}

// In form builder:
$builder->add('tags', TextType::class);
$builder->get('tags')->addModelTransformer(new TagsTransformer());
```

## Rules

- Always create dedicated Form Type classes
- Use `EntityType` for Doctrine relationships
- Use `CollectionType` for dynamic field lists
- Use form events for dynamic/dependent fields
- Use data transformers for complex type conversions
- Set `data_class` in `configureOptions`
- Use `#[Assert\*]` constraints on the entity, not in the form
