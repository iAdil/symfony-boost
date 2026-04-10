<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Support;

class Config
{
    private ?array $data = null;

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public function path(): string
    {
        return $this->projectDir.\DIRECTORY_SEPARATOR.'boost.json';
    }

    public function exists(): bool
    {
        return file_exists($this->path());
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $data = $this->read();

        return $data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $data = $this->read();
        $data[$key] = $value;
        $this->write($data);
    }

    public function read(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        if (!$this->exists()) {
            return $this->data = [];
        }

        $content = file_get_contents($this->path());
        $decoded = json_decode($content, true);

        return $this->data = \is_array($decoded) ? $decoded : [];
    }

    public function write(array $data): void
    {
        ksort($data);

        $data = array_filter($data, fn ($value) => $value !== null && $value !== [] && $value !== '');

        $this->data = $data;

        file_put_contents(
            $this->path(),
            json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)."\n"
        );
    }
}
