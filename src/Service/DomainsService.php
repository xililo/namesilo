<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Response\ApiResponse;
use Xililo\Namesilo\ValueObject\DomainRegistration;

final class DomainsService extends AbstractService
{
    public function checkAvailability(string|array $domains): ApiResponse
    {
        return $this->call('checkRegisterAvailability', [
            'domains' => is_array($domains) ? implode(',', $domains) : $domains,
        ]);
    }

    public function register(string $domain, int $years, ?DomainRegistration $registration = null): ApiResponse
    {
        return $this->call('registerDomain', array_merge([
            'domain' => $domain,
            'years' => $years,
        ], $registration?->toArray() ?? []));
    }

    public function renew(string $domain, int $years, array $options = []): ApiResponse
    {
        return $this->call('renewDomain', array_merge([
            'domain' => $domain,
            'years' => $years,
        ], $options));
    }

    public function list(?string $portfolio = null): ApiResponse
    {
        return $this->call('listDomains', array_filter([
            'portfolio' => $portfolio,
        ], static fn (mixed $value): bool => $value !== null));
    }

    public function info(string $domain): ApiResponse
    {
        return $this->call('getDomainInfo', [
            'domain' => $domain,
        ]);
    }

    public function lock(string $domain): ApiResponse
    {
        return $this->call('domainLock', [
            'domain' => $domain,
        ]);
    }

    public function unlock(string $domain): ApiResponse
    {
        return $this->call('domainUnlock', [
            'domain' => $domain,
        ]);
    }

    public function addPrivacy(string $domain): ApiResponse
    {
        return $this->call('addPrivacy', [
            'domain' => $domain,
        ]);
    }

    public function removePrivacy(string $domain): ApiResponse
    {
        return $this->call('removePrivacy', [
            'domain' => $domain,
        ]);
    }

    public function addAutoRenew(string $domain): ApiResponse
    {
        return $this->call('addAutoRenew', [
            'domain' => $domain,
        ]);
    }

    public function removeAutoRenew(string $domain): ApiResponse
    {
        return $this->call('removeAutoRenew', [
            'domain' => $domain,
        ]);
    }

    public function retrieveAuthCode(string $domain): ApiResponse
    {
        return $this->call('retrieveAuthCode', [
            'domain' => $domain,
        ]);
    }
}
