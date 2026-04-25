<?php

declare(strict_types=1);

namespace Xililo\Namesilo\ValueObject;

final class TransferRequest
{
    /**
     * @param array<string, scalar|null> $attributes
     */
    public function __construct(
        private readonly array $attributes = []
    ) {
    }

    /**
     * @return array<string, scalar|null>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
