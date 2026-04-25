<?php

declare(strict_types=1);

namespace Xililo\Namesilo;

use Xililo\Namesilo\Contracts\Transport;
use Xililo\Namesilo\Exception\ApiException;
use Xililo\Namesilo\Exception\ResponseParsingException;
use Xililo\Namesilo\Http\CurlTransport;
use Xililo\Namesilo\Response\ApiResponse;
use Xililo\Namesilo\Service\AccountService;
use Xililo\Namesilo\Service\ContactsService;
use Xililo\Namesilo\Service\DnsService;
use Xililo\Namesilo\Service\DomainsService;
use Xililo\Namesilo\Service\NameServersService;
use Xililo\Namesilo\Service\TransfersService;

final class NamesiloClient
{
    private const BASE_URL = 'https://www.namesilo.com/api';

    public function __construct(
        private readonly string $apiKey,
        private readonly Transport $transport = new CurlTransport(),
        private readonly bool $throwOnApiError = true,
        private readonly int $version = 1
    ) {
    }

    public function domains(): DomainsService
    {
        return new DomainsService($this);
    }

    public function transfers(): TransfersService
    {
        return new TransfersService($this);
    }

    public function contacts(): ContactsService
    {
        return new ContactsService($this);
    }

    public function nameServers(): NameServersService
    {
        return new NameServersService($this);
    }

    public function dns(): DnsService
    {
        return new DnsService($this);
    }

    public function account(): AccountService
    {
        return new AccountService($this);
    }

    /**
     * @param array<string, scalar|null> $params
     */
    public function request(string $operation, array $params = []): ApiResponse
    {
        $payload = array_filter(
            array_merge(
                [
                    'version' => $this->version,
                    'type' => 'json',
                    'key' => $this->apiKey,
                ],
                $params
            ),
            static fn (mixed $value): bool => $value !== null
        );

        $body = $this->transport->get(self::BASE_URL . '/' . $operation, $payload);
        $decoded = json_decode($body, true);

        if (! is_array($decoded)) {
            throw new ResponseParsingException('Unable to decode NameSilo JSON response.');
        }

        $request = is_array($decoded['request'] ?? null) ? $decoded['request'] : [];
        $reply = is_array($decoded['reply'] ?? null) ? $decoded['reply'] : [];

        $response = new ApiResponse($request, $reply, $decoded);

        if ($this->throwOnApiError && ! $response->isSuccess()) {
            throw new ApiException($response);
        }

        return $response;
    }
}
