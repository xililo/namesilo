<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Response;

final class ApiResponse
{
    /**
     * @param array<string, mixed> $request
     * @param array<string, mixed> $reply
     * @param array<string, mixed> $raw
     */
    public function __construct(
        private readonly array $request,
        private readonly array $reply,
        private readonly array $raw
    ) {
    }

    public function code(): int
    {
        return (int) ($this->reply['code'] ?? 0);
    }

    public function detail(): string
    {
        return (string) ($this->reply['detail'] ?? '');
    }

    public function operation(): string
    {
        return (string) ($this->request['operation'] ?? '');
    }

    public function isSuccess(): bool
    {
        return $this->code() >= 300 && $this->code() < 400;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $data = $this->reply;
        unset($data['code'], $data['detail']);

        return $data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->reply[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function request(): array
    {
        return $this->request;
    }

    /**
     * @return array<string, mixed>
     */
    public function reply(): array
    {
        return $this->reply;
    }

    /**
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->raw;
    }
}
