<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Tests;

use Xililo\Namesilo\Exception\InvalidArgumentException;
use Xililo\Namesilo\Http\MockTransport;
use Xililo\Namesilo\NamesiloClient;
use Xililo\Namesilo\ValueObject\ContactProfile;
use Xililo\Namesilo\ValueObject\DnsRecord;
use Xililo\Namesilo\ValueObject\DomainRegistration;

final class ServiceTest extends TestCase
{
    public function testNameserverChangeMapsIndexedParams(): void
    {
        $transport = new MockTransport([
            'changeNameServers' => '{"request":{"operation":"changeNameServers"},"reply":{"code":300,"detail":"success"}}',
        ]);

        $client = new NamesiloClient('key', $transport);
        $client->nameServers()->change('example.com', ['ns1.host.com', 'ns2.host.com']);

        $request = $transport->requests()[0]['query'];

        $this->assertSame('example.com', $request['domain']);
        $this->assertSame('ns1.host.com', $request['ns1']);
        $this->assertSame('ns2.host.com', $request['ns2']);
    }

    public function testNameserverChangeRejectsInvalidCount(): void
    {
        $client = new NamesiloClient('key', new MockTransport());

        try {
            $client->nameServers()->change('example.com', ['ns1.host.com']);
        } catch (InvalidArgumentException $exception) {
            $this->assertTrue(str_contains($exception->getMessage(), 'between 2 and 13'));

            return;
        }

        throw new \RuntimeException('Expected InvalidArgumentException was not thrown.');
    }

    public function testContactAndDnsPayloadsPassThrough(): void
    {
        $transport = new MockTransport([
            'contactAdd' => '{"request":{"operation":"contactAdd"},"reply":{"code":300,"detail":"success","contact_id":123}}',
            'dnsAddRecord' => '{"request":{"operation":"dnsAddRecord"},"reply":{"code":300,"detail":"success","record_id":"abc"}}',
            'registerDomain' => '{"request":{"operation":"registerDomain"},"reply":{"code":300,"detail":"success","domain":"example.com"}}',
        ]);

        $client = new NamesiloClient('key', $transport);

        $client->contacts()->add(new ContactProfile([
            'fn' => 'Ada',
            'ln' => 'Lovelace',
            'ad' => '123 Main',
            'cy' => 'Lusaka',
            'st' => 'Lusaka',
            'zp' => '10101',
            'ct' => 'ZM',
            'em' => 'ada@example.com',
            'ph' => '5551234567',
        ]));

        $client->dns()->addRecord('example.com', new DnsRecord([
            'rrtype' => 'A',
            'rrhost' => 'www',
            'rrvalue' => '203.0.113.10',
        ]));

        $client->domains()->register('example.com', 1, new DomainRegistration([
            'private' => 1,
            'auto_renew' => 1,
        ]));

        $requests = $transport->requests();

        $this->assertSame('Ada', $requests[0]['query']['fn']);
        $this->assertSame('A', $requests[1]['query']['rrtype']);
        $this->assertSame(1, $requests[2]['query']['years']);
        $this->assertSame(1, $requests[2]['query']['private']);
    }
}
