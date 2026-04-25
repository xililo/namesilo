<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Response\ApiResponse;

final class NameServersService extends AbstractService
{
    /**
     * @param list<string> $nameservers
     */
    public function change(string $domain, array $nameservers): ApiResponse
    {
        return $this->call('changeNameServers', array_merge([
            'domain' => $domain,
        ], $this->nameserverParams($nameservers)));
    }

    public function listRegistered(string $domain): ApiResponse
    {
        return $this->call('listRegisteredNameServers', [
            'domain' => $domain,
        ]);
    }

    public function register(string $domain, string $hostname, string $ip): ApiResponse
    {
        return $this->call('registerNameServer', [
            'domain' => $domain,
            'new_host' => $hostname,
            'ip1' => $ip,
        ]);
    }

    public function modify(string $domain, string $hostname, string $newIp): ApiResponse
    {
        return $this->call('modifyNameServer', [
            'domain' => $domain,
            'current_host' => $hostname,
            'ip1' => $newIp,
        ]);
    }

    public function delete(string $domain, string $hostname): ApiResponse
    {
        return $this->call('deleteNameServer', [
            'domain' => $domain,
            'host' => $hostname,
        ]);
    }
}
