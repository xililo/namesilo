# Xililo NameSilo PHP

A clean Composer package for the [NameSilo API](https://www.namesilo.com/api-reference).

Full documentation is available in [docs/USAGE.md](/docs/USAGE.md).

## Features

- Domain operations
- Transfer operations
- Contact operations
- NameServer operations
- DNS operations
- Account operations
- JSON-first response parsing
- Small transport layer for easy testing

## Install

```bash
composer require xililo/namesilo
```

## Quick Start

```php
<?php

declare(strict_types=1);

use Xililo\Namesilo\NamesiloClient;
use Xililo\Namesilo\ValueObject\DnsRecord;

$client = new NamesiloClient('your-api-key');

$availability = $client->domains()->checkAvailability(['example.com', 'example.net']);
$domainInfo = $client->domains()->info('example.com');

$client->dns()->addRecord('example.com', new DnsRecord([
    'rrtype' => 'A',
    'rrhost' => 'www',
    'rrvalue' => '203.0.113.10',
    'rrttl' => 3600,
]));
```

## Services

### Domains

```php
$client->domains()->checkAvailability(['example.com']);
$client->domains()->register('example.com', 1);
$client->domains()->renew('example.com', 1);
$client->domains()->list();
$client->domains()->info('example.com');
$client->domains()->lock('example.com');
$client->domains()->unlock('example.com');
$client->domains()->addPrivacy('example.com');
$client->domains()->removePrivacy('example.com');
$client->domains()->addAutoRenew('example.com');
$client->domains()->removeAutoRenew('example.com');
$client->domains()->retrieveAuthCode('example.com');
```

### Transfers

```php
$client->transfers()->checkAvailability(['example.com']);
$client->transfers()->transfer('example.com');
$client->transfers()->checkStatus('example.com');
$client->transfers()->updateEppCode('example.com', 'auth-code');
$client->transfers()->resendAdminEmail('example.com');
$client->transfers()->resubmit('example.com');
```

### Contacts

```php
use Xililo\Namesilo\ValueObject\ContactProfile;

$profile = new ContactProfile([
    'fn' => 'Ada',
    'ln' => 'Lovelace',
    'ad' => '123 Main Street',
    'cy' => 'Lusaka',
    'st' => 'Lusaka',
    'zp' => '10101',
    'ct' => 'ZM',
    'em' => 'ada@example.com',
    'ph' => '5551234567',
]);

$client->contacts()->list();
$client->contacts()->add($profile);
$client->contacts()->update(12345, $profile);
$client->contacts()->delete(12345);
$client->contacts()->associateWithDomain('example.com', 12345);
```

### NameServers

```php
$client->nameServers()->change('example.com', [
    'ns1.example-host.com',
    'ns2.example-host.com',
]);

$client->nameServers()->listRegistered('example.com');
$client->nameServers()->register('example.com', 'ns1', '203.0.113.10');
$client->nameServers()->modify('example.com', 'ns1', '203.0.113.11');
$client->nameServers()->delete('example.com', 'ns1');
```

### DNS

```php
use Xililo\Namesilo\ValueObject\DnsRecord;

$record = new DnsRecord([
    'rrtype' => 'TXT',
    'rrhost' => '@',
    'rrvalue' => 'v=spf1 include:_spf.example.com ~all',
    'rrttl' => 3600,
]);

$client->dns()->listRecords('example.com');
$client->dns()->addRecord('example.com', $record);
$client->dns()->updateRecord('example.com', 'record-id', $record);
$client->dns()->deleteRecord('example.com', 'record-id');
```

### Account

```php
$client->account()->prices();
$client->account()->fundsBalance();
$client->account()->addFunds(50.00);
$client->account()->listOrders();
$client->account()->orderDetails(123456);
```

## Responses

Every service method returns `Xililo\Namesilo\Response\ApiResponse`.

```php
$response = $client->domains()->info('example.com');

$response->isSuccess();
$response->code();
$response->detail();
$response->data();
$response->get('expires');
```

By default, non-success NameSilo reply codes throw `Xililo\Namesilo\Exception\ApiException`.

## Notes About Operation Mapping

Most operation names in this package were verified against NameSilo's official API reference and support index. A few names in the `NameServers` and `Account` areas are based on NameSilo's support article titles and their existing naming pattern because those individual reference pages are harder to access without JavaScript.

That means the package structure is solid, but you should validate the less common operation names against your account before shipping production code around those endpoints.

## Run Tests

```bash
composer test
```
