<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Tests;

use Xililo\Namesilo\Http\MockTransport;
use Xililo\Namesilo\NamesiloClient;

final class ClientTest extends TestCase
{
    public function testClientBuildsExpectedQueryAndParsesResponse(): void
    {
        $transport = new MockTransport([
            'getDomainInfo' => '{"request":{"operation":"getDomainInfo","ip":"127.0.0.1"},"reply":{"code":300,"detail":"success","domain":"example.com","expires":"2030-01-01"}}',
        ]);

        $client = new NamesiloClient('test-key', $transport);
        $response = $client->domains()->info('example.com');

        $this->assertTrue($response->isSuccess());
        $this->assertSame(300, $response->code());
        $this->assertSame('example.com', $response->get('domain'));

        $requests = $transport->requests();
        $this->assertCount(1, $requests);
        $this->assertSame('test-key', $requests[0]['query']['key']);
        $this->assertSame('json', $requests[0]['query']['type']);
        $this->assertSame('example.com', $requests[0]['query']['domain']);
    }
}
