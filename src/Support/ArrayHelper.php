<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Support;

final class ArrayHelper
{
    /**
     * @param array<int|string, mixed> $value
     * @return list<array<string, mixed>>
     */
    public static function listOfAssoc(array $value): array
    {
        if ($value === []) {
            return [];
        }

        $first = reset($value);

        if (is_array($first)) {
            /** @var list<array<string, mixed>> $value */
            return array_values($value);
        }

        /** @var array<string, mixed> $value */
        return [$value];
    }
}
