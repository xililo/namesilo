<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Response\ApiResponse;

final class AccountService extends AbstractService
{
    public function prices(bool $retailPrices = false, ?int $registrationDomains = null): ApiResponse
    {
        return $this->call('getPrices', array_filter([
            'retail_prices' => $retailPrices ? 1 : null,
            'registration_domains' => $registrationDomains,
        ], static fn (mixed $value): bool => $value !== null));
    }

    public function fundsBalance(): ApiResponse
    {
        return $this->call('getAccountFundsBalance');
    }

    public function addFunds(float $amount, ?string $paymentId = null): ApiResponse
    {
        return $this->call('addAccountFunds', array_filter([
            'amount' => $amount,
            'payment_id' => $paymentId,
        ], static fn (mixed $value): bool => $value !== null));
    }

    public function listOrders(?int $page = null): ApiResponse
    {
        return $this->call('listOrders', array_filter([
            'page' => $page,
        ], static fn (mixed $value): bool => $value !== null));
    }

    public function orderDetails(int|string $orderNumber): ApiResponse
    {
        return $this->call('getOrderDetails', [
            'order_number' => $orderNumber,
        ]);
    }
}
