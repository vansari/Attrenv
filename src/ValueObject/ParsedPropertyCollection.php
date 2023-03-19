<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\ValueObject;

/**
 * @template-implements \Iterator<int, string|null>
 */
class ParsedPropertyCollection implements \Iterator
{
    private array $properties = [];

    private int $pos = 0;

    public function add(string $propertyName): self
    {
        if (!$this->has($propertyName)) {
            $this->properties[] = $propertyName;
        }

        return $this;
    }

    public function get(int $index): ?string
    {
        return $this->properties[$index] ?? null;
    }

    public function has(string $propertyName): bool
    {
        return in_array($propertyName, $this->properties, true);
    }

    public function current(): ?string
    {
        return $this->get($this->pos);
    }

    public function next(): void
    {
        $this->pos++;
    }

    public function key(): mixed
    {
        return $this->pos;
    }

    public function valid(): bool
    {
        return $this->pos < count($this->properties);
    }

    public function rewind(): void
    {
        $this->pos = 0;
    }
}
