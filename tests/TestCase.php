<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Tests;

abstract class TestCase
{
    protected function assertTrue(bool $condition, string $message = 'Expected condition to be true.'): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }

    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            throw new \RuntimeException($message !== '' ? $message : sprintf('Expected %s, got %s.', var_export($expected, true), var_export($actual, true)));
        }
    }

    protected function assertCount(int $expected, array|\Countable $actual, string $message = ''): void
    {
        $count = count($actual);

        if ($count !== $expected) {
            throw new \RuntimeException($message !== '' ? $message : sprintf('Expected count %d, got %d.', $expected, $count));
        }
    }
}
