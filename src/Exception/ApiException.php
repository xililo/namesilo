<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Exception;

use Xililo\Namesilo\Response\ApiResponse;
use RuntimeException;

final class ApiException extends RuntimeException
{
    public function __construct(
        private readonly ApiResponse $response,
        ?string $message = null
    ) {
        parent::__construct($message ?? $response->detail(), $response->code());
    }

    public function response(): ApiResponse
    {
        return $this->response;
    }
}
