<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\ValueObject;

final class Type
{
    public function __construct(
        private readonly string $type,
        private readonly bool $allowsNull
    ) {
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
