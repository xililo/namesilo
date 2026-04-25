<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Http;

use Xililo\Namesilo\Contracts\Transport;
use Xililo\Namesilo\Exception\TransportException;

final class CurlTransport implements Transport
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function get(string $url, array $query): string
    {
        $endpoint = $url . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $handle = curl_init($endpoint);

        if ($handle === false) {
            throw new TransportException('Unable to initialize cURL.');
        }

        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => 'xililo/namesilo',
            CURLOPT_HTTPGET => true,
        ]);

        $body = curl_exec($handle);

        if ($body === false) {
            $message = curl_error($handle);
            curl_close($handle);

            throw new TransportException($message !== '' ? $message : 'Unknown transport error.');
        }

        $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        if ($statusCode >= 400) {
            throw new TransportException(sprintf('NameSilo responded with HTTP %d.', $statusCode));
        }

        return $body;
    }
}
