---
name: create-test
description: Create unit, integration, and functional tests following Symfony best practices
---
# Create Tests

## Unit Test (no kernel)

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\PriceCalculator;
use PHPUnit\Framework\TestCase;

final class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    public function testCalculateWithTax(): void
    {
        $result = $this->calculator->calculateWithTax(100.0, 0.21);

        self::assertSame(121.0, $result);
    }

    public function testCalculateWithZeroTax(): void
    {
        $result = $this->calculator->calculateWithTax(100.0, 0.0);

        self::assertSame(100.0, $result);
    }

    public function testNegativePriceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->calculateWithTax(-10.0, 0.21);
    }
}
```

## Integration Test (with kernel)

```php
<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = static::getContainer()->get(UserRepository::class);
    }

    public function testFindByEmail(): void
    {
        $user = $this->repository->findOneBy(['email' => 'test@example.com']);

        self::assertInstanceOf(User::class, $user);
        self::assertSame('test@example.com', $user->getEmail());
    }
}
```

## Functional Test (HTTP)

```php
<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    public function testListPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Users');
    }

    public function testCreateUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/new');

        $form = $crawler->selectButton('Save')->form([
            'user[name]' => 'John Doe',
            'user[email]' => 'john@example.com',
        ]);

        $client->submit($form);

        self::assertResponseRedirects('/users');
        $client->followRedirect();
        self::assertSelectorTextContains('.flash-success', 'created');
    }

    public function testApiEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/users', server: [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertIsArray($data);
    }

    public function testApiCreate(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/users', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]));

        self::assertResponseStatusCodeSame(201);
    }

    public function testUnauthorizedAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/dashboard');

        self::assertResponseRedirects('/login');
    }
}
```

## Rules

- Use `self::assert*` (static calls) — not `$this->assert*`
- Unit tests: no kernel, test pure logic, fast
- Integration tests: boot kernel, test service wiring and DB queries
- Functional tests: use HTTP client, test full request/response cycle
- One assertion concept per test method
- Use `setUp()` for shared initialization
- Use data providers for parameterized tests
- Mock external services, not internal ones
- Run with: `php bin/phpunit`
