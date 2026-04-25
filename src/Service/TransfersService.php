<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Response\ApiResponse;
use Xililo\Namesilo\ValueObject\TransferRequest;

final class TransfersService extends AbstractService
{
    public function checkAvailability(string|array $domains): ApiResponse
    {
        return $this->call('checkTransferAvailability', [
            'domains' => is_array($domains) ? implode(',', $domains) : $domains,
        ]);
    }

    public function transfer(string $domain, ?TransferRequest $transfer = null): ApiResponse
    {
        return $this->call('transferDomain', array_merge([
            'domain' => $domain,
        ], $transfer?->toArray() ?? []));
    }

    public function checkStatus(string $domain): ApiResponse
    {
        return $this->call('checkTransferStatus', [
            'domain' => $domain,
        ]);
    }

    public function updateEppCode(string $domain, string $authCode): ApiResponse
    {
        return $this->call('transferUpdateEppCode', [
            'domain' => $domain,
            'auth' => $authCode,
        ]);
    }

    public function resendAdminEmail(string $domain): ApiResponse
    {
        return $this->call('transferResendAdminEmail', [
            'domain' => $domain,
        ]);
    }

    public function resubmit(string $domain): ApiResponse
    {
        return $this->call('transferResubmit', [
            'domain' => $domain,
        ]);
    }
}
