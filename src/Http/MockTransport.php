<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Http;

use Xililo\Namesilo\Contracts\Transport;

final class MockTransport implements Transport
{
    /**
     * @var list<array{url: string, query: array<string, scalar|null>}>
     */
    private array $requests = [];

    /**
     * @param array<string, string> $responses
     */
    public function __construct(
        private array $responses = []
    ) {
    }

    /**
     * @param array<string, scalar|null> $query
     */
    public function get(string $url, array $query): string
    {
        $operation = (string) preg_replace('~^.*/~', '', $url);

        $this->requests[] = [
            'url' => $url,
            'query' => $query,
        ];

        return $this->responses[$operation] ?? '{"request":{"operation":"unknown"},"reply":{"code":300,"detail":"success"}}';
    }

    /**
     * @return list<array{url: string, query: array<string, scalar|null>}>
     */
    public function requests(): array
    {
        return $this->requests;
    }
}
