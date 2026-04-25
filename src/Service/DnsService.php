<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Response\ApiResponse;
use Xililo\Namesilo\ValueObject\DnsRecord;

final class DnsService extends AbstractService
{
    public function listRecords(string $domain): ApiResponse
    {
        return $this->call('dnsListRecords', [
            'domain' => $domain,
        ]);
    }

    public function addRecord(string $domain, DnsRecord $record): ApiResponse
    {
        return $this->call('dnsAddRecord', array_merge([
            'domain' => $domain,
        ], $record->toArray()));
    }

    public function updateRecord(string $domain, string $recordId, DnsRecord $record): ApiResponse
    {
        return $this->call('dnsUpdateRecord', array_merge([
            'domain' => $domain,
            'rrid' => $recordId,
        ], $record->toArray()));
    }

    public function deleteRecord(string $domain, string $recordId): ApiResponse
    {
        return $this->call('dnsDeleteRecord', [
            'domain' => $domain,
            'rrid' => $recordId,
        ]);
    }
}
