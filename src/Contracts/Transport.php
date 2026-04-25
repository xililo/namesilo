<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Contracts;

interface Transport
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function get(string $url, array $query): string;
}
