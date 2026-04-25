<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Exception\InvalidArgumentException;
use Xililo\Namesilo\NamesiloClient;

abstract class AbstractService
{
    public function __construct(
        protected readonly NamesiloClient $client
    ) {
    }

    /**
     * @param list<string> $nameservers
     * @return array<string, string>
     */
    protected function nameserverParams(array $nameservers): array
    {
        $count = count($nameservers);

        if ($count < 2 || $count > 13) {
            throw new InvalidArgumentException('You must provide between 2 and 13 nameservers.');
        }

        $params = [];

        foreach (array_values($nameservers) as $index => $nameserver) {
            $params['ns' . ($index + 1)] = $nameserver;
        }

        return $params;
    }

    /**
     * @param array<string, scalar|null> $params
     */
    protected function call(string $operation, array $params = []): \Xililo\Namesilo\Response\ApiResponse
    {
        return $this->client->request($operation, $params);
    }
}
