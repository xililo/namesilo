# Xililo NameSilo Usage Guide

This document explains how to use the package in detail:

- how to create the client
- what each service does
- which arguments are required
- which arguments are optional
- how to use the provided value objects
- how to work with returned `ApiResponse` objects

All examples use the `Xililo\Namesilo\...` namespace.

## Installation

```bash
composer require xililo/namesilo
```

## Basic Setup

### Create a client

```php
<?php

declare(strict_types=1);

use Xililo\Namesilo\NamesiloClient;

$client = new NamesiloClient('your-namesilo-api-key');
```

### What the client does

The client:

- sends requests to the NameSilo API
- automatically uses JSON responses
- returns `Xililo\Namesilo\Response\ApiResponse`
- throws `Xililo\Namesilo\Exception\ApiException` when NameSilo returns a non-success reply code

### Accessing services

```php
$client->domains();
$client->transfers();
$client->contacts();
$client->nameServers();
$client->dns();
$client->account();
```

## Returned Response Object

Every service method returns an `ApiResponse`.

```php
use Xililo\Namesilo\Response\ApiResponse;

$response = $client->domains()->info('example.com');
```

### Available response methods

#### `isSuccess(): bool`

```php
if ($response->isSuccess()) {
    // request succeeded
}
```

#### `code(): int`

```php
$code = $response->code();
```

#### `detail(): string`

```php
$detail = $response->detail();
```

#### `operation(): string`

```php
$operation = $response->operation();
```

#### `get(string $key, mixed $default = null): mixed`

```php
$expires = $response->get('expires');
$fallback = $response->get('missing_key', 'not-set');
```

#### `data(): array`

This returns the reply body without `code` and `detail`.

```php
$data = $response->data();
```

#### `request(): array`

Returns the decoded `request` part from NameSilo.

```php
$request = $response->request();
```

#### `reply(): array`

Returns the full decoded `reply` part.

```php
$reply = $response->reply();
```

#### `raw(): array`

Returns the full decoded response body.

```php
$raw = $response->raw();
```

### Example: handling a response

```php
$response = $client->domains()->info('example.com');

echo $response->code() . PHP_EOL;
echo $response->detail() . PHP_EOL;
echo $response->get('expires') . PHP_EOL;

if ($response->isSuccess()) {
    $data = $response->data();

    print_r($data);
}
```

## Error Handling

### API errors

If NameSilo returns a non-success reply code, the client throws `ApiException`.

```php
use Xililo\Namesilo\Exception\ApiException;

try {
    $client->domains()->register('example.com', 1);
} catch (ApiException $exception) {
    $response = $exception->response();

    echo $response->code() . PHP_EOL;
    echo $response->detail() . PHP_EOL;
}
```

### Transport errors

Network or HTTP transport issues throw `TransportException`.

### Response parsing errors

Invalid JSON responses throw `ResponseParsingException`.

## Value Objects

This package includes value objects to keep request payloads readable.

## `ContactProfile`

Namespace:

```php
use Xililo\Namesilo\ValueObject\ContactProfile;
```

Used for:

- `contacts()->add()`
- `contacts()->update()`

### Required fields

These are required by the package examples and match the common NameSilo contact requirements:

- `fn` - first name
- `ln` - last name
- `ad` - address line 1
- `cy` - city
- `st` - state / province / territory
- `zp` - zip / postal code
- `ct` - country code
- `em` - email
- `ph` - phone

### Optional fields

- `nn` - nickname
- `cp` - company
- `ad2` - address line 2
- `fx` - fax
- `usnc` - .US nexus category
- `usap` - .US application purpose
- `calf` - .CA legal form
- `caln` - .CA language
- `caag` - .CA agreement version
- `cawd` - .CA whois display
- `eucs` - .EU citizenship country abbreviation

### Example

```php
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

    // optional
    'nn' => 'Primary Contact',
    'cp' => 'Analytical Engines Ltd',
]);
```

### Convert to array

```php
$payload = $profile->toArray();
```

## `DnsRecord`

Namespace:

```php
use Xililo\Namesilo\ValueObject\DnsRecord;
```

Used for:

- `dns()->addRecord()`
- `dns()->updateRecord()`

### Common required fields

- `rrtype` - record type such as `A`, `AAAA`, `CNAME`, `MX`, `TXT`, `SRV`, `CAA`
- `rrhost` - record host
- `rrvalue` - record value

### Optional fields

- `rrdistance` - mostly used for `MX`
- `rrttl` - TTL

### Example: A record

```php
$record = new DnsRecord([
    'rrtype' => 'A',
    'rrhost' => 'www',
    'rrvalue' => '203.0.113.10',
    'rrttl' => 3600,
]);
```

### Example: MX record

```php
$record = new DnsRecord([
    'rrtype' => 'MX',
    'rrhost' => '@',
    'rrvalue' => 'mail.example.com',
    'rrdistance' => 10,
    'rrttl' => 3600,
]);
```

## `DomainRegistration`

Namespace:

```php
use Xililo\Namesilo\ValueObject\DomainRegistration;
```

Used for:

- `domains()->register()`

### Required fields in the method

The method itself requires:

- `domain`
- `years`

### Optional fields in the value object

Common optional fields:

- `payment_id`
- `private`
- `auto_renew`
- `portfolio`
- `coupon`
- `contact_id`
- `ns1` ... `ns13`
- all supported contact payload fields such as `fn`, `ln`, `ad`, `cy`, `st`, `zp`, `ct`, `em`, `ph`

### Example

```php
$registration = new DomainRegistration([
    'private' => 1,
    'auto_renew' => 1,
    'portfolio' => 'Main Portfolio',
    'contact_id' => 123456,
    'ns1' => 'ns1.host.com',
    'ns2' => 'ns2.host.com',
]);

$response = $client->domains()->register('example.com', 1, $registration);
```

## `TransferRequest`

Namespace:

```php
use Xililo\Namesilo\ValueObject\TransferRequest;
```

Used for:

- `transfers()->transfer()`

### Required fields in the method

The method itself requires:

- `domain`

### Optional fields in the value object

Common optional fields:

- `payment_id`
- `auth`
- `private`
- `auto_renew`
- `portfolio`
- `coupon`
- `contact_id`
- `ns1` ... `ns13`
- contact data fields where supported

### Example

```php
$transfer = new TransferRequest([
    'auth' => 'your-epp-code',
    'private' => 1,
    'auto_renew' => 1,
    'portfolio' => 'Transferred Domains',
]);

$response = $client->transfers()->transfer('example.com', $transfer);
```

## Domains Service

Namespace is not needed directly in use statements because you reach it through the client:

```php
$domains = $client->domains();
```

## `checkAvailability(string|array $domains)`

Checks registration availability.

### Required

- `domains` - a string domain or an array of domains

### Optional

- none

### Example

```php
$response = $client->domains()->checkAvailability([
    'example.com',
    'example.net',
]);
```

### Example response usage

```php
if ($response->isSuccess()) {
    print_r($response->data());
}
```

## `register(string $domain, int $years, ?DomainRegistration $registration = null)`

Registers a domain.

### Required

- `domain`
- `years`

### Optional

- `registration`

### Example

```php
$response = $client->domains()->register(
    'example.com',
    1,
    new DomainRegistration([
        'private' => 1,
        'auto_renew' => 1,
    ])
);
```

### Example response usage

```php
echo $response->get('domain') . PHP_EOL;
echo $response->get('order_amount') . PHP_EOL;
echo $response->get('message') . PHP_EOL;
```

## `renew(string $domain, int $years, array $options = [])`

Renews a domain.

### Required

- `domain`
- `years`

### Optional

- `options`
  - `payment_id`
  - `coupon`

### Example

```php
$response = $client->domains()->renew('example.com', 1, [
    'coupon' => 'SAVE10',
]);
```

## `list(?string $portfolio = null)`

Lists domains.

### Required

- none

### Optional

- `portfolio`

### Example

```php
$response = $client->domains()->list();
$response = $client->domains()->list('Main Portfolio');
```

## `info(string $domain)`

Gets detailed information about a domain.

### Required

- `domain`

### Optional

- none

### Example

```php
$response = $client->domains()->info('example.com');

echo $response->get('status') . PHP_EOL;
echo $response->get('expires') . PHP_EOL;
echo $response->get('auto_renew') . PHP_EOL;
```

## `lock(string $domain)` and `unlock(string $domain)`

### Required

- `domain`

### Optional

- none

### Example

```php
$client->domains()->lock('example.com');
$client->domains()->unlock('example.com');
```

## `addPrivacy(string $domain)` and `removePrivacy(string $domain)`

### Required

- `domain`

## `addAutoRenew(string $domain)` and `removeAutoRenew(string $domain)`

### Required

- `domain`

## `retrieveAuthCode(string $domain)`

Gets the EPP / auth code through the API operation mapping used by this package.

### Required

- `domain`

### Example

```php
$response = $client->domains()->retrieveAuthCode('example.com');
print_r($response->data());
```

## Transfers Service

```php
$transfers = $client->transfers();
```

## `checkAvailability(string|array $domains)`

Checks transfer availability.

### Required

- `domains`

### Example

```php
$response = $client->transfers()->checkAvailability([
    'example.com',
    'example.net',
]);
```

## `transfer(string $domain, ?TransferRequest $transfer = null)`

Creates a transfer request.

### Required

- `domain`

### Optional

- `transfer`

### Example

```php
$response = $client->transfers()->transfer(
    'example.com',
    new TransferRequest([
        'auth' => 'my-epp-code',
        'private' => 1,
    ])
);
```

## `checkStatus(string $domain)`

Checks transfer status.

### Required

- `domain`

### Example

```php
$response = $client->transfers()->checkStatus('example.com');

echo $response->get('status') . PHP_EOL;
echo $response->get('message') . PHP_EOL;
echo $response->get('date') . PHP_EOL;
```

## `updateEppCode(string $domain, string $authCode)`

### Required

- `domain`
- `authCode`

### Example

```php
$client->transfers()->updateEppCode('example.com', 'new-auth-code');
```

## `resendAdminEmail(string $domain)`

### Required

- `domain`

## `resubmit(string $domain)`

### Required

- `domain`

## Contacts Service

```php
$contacts = $client->contacts();
```

## `list(int|string|null $contactId = null, ?int $offset = null)`

Lists contact profiles.

### Required

- none

### Optional

- `contactId`
- `offset`

### Example

```php
$all = $client->contacts()->list();
$one = $client->contacts()->list(12345);
$paged = $client->contacts()->list(null, 1000);
```

### Example response usage

```php
$contacts = $all->get('contact', []);
print_r($contacts);
```

## `add(ContactProfile $profile)`

### Required

- `profile`

### Example

```php
$response = $client->contacts()->add($profile);
echo $response->get('contact_id') . PHP_EOL;
```

## `update(int|string $contactId, ContactProfile $profile)`

### Required

- `contactId`
- `profile`

## `delete(int|string $contactId)`

### Required

- `contactId`

## `associateWithDomain(string $domain, int|string $contactId)`

### Required

- `domain`
- `contactId`

### Example

```php
$client->contacts()->associateWithDomain('example.com', 12345);
```

## NameServers Service

```php
$nameServers = $client->nameServers();
```

## `change(string $domain, array $nameservers)`

Changes the active nameservers on a domain.

### Required

- `domain`
- `nameservers`

### Rules

- minimum 2 nameservers
- maximum 13 nameservers

### Optional

- none

### Example

```php
$response = $client->nameServers()->change('example.com', [
    'ns1.host.com',
    'ns2.host.com',
]);
```

## `listRegistered(string $domain)`

Lists registered child nameservers for a domain.

### Required

- `domain`

## `register(string $domain, string $hostname, string $ip)`

Registers a nameserver host.

### Required

- `domain`
- `hostname`
- `ip`

### Example

```php
$client->nameServers()->register('example.com', 'ns1', '203.0.113.10');
```

## `modify(string $domain, string $hostname, string $newIp)`

### Required

- `domain`
- `hostname`
- `newIp`

### Example

```php
$client->nameServers()->modify('example.com', 'ns1', '203.0.113.11');
```

## `delete(string $domain, string $hostname)`

### Required

- `domain`
- `hostname`

## DNS Service

```php
$dns = $client->dns();
```

## `listRecords(string $domain)`

Lists DNS records.

### Required

- `domain`

### Example

```php
$response = $client->dns()->listRecords('example.com');
$records = $response->get('resource_record', []);
print_r($records);
```

## `addRecord(string $domain, DnsRecord $record)`

### Required

- `domain`
- `record`

### Example

```php
$response = $client->dns()->addRecord(
    'example.com',
    new DnsRecord([
        'rrtype' => 'TXT',
        'rrhost' => '@',
        'rrvalue' => 'v=spf1 include:_spf.example.com ~all',
        'rrttl' => 3600,
    ])
);

echo $response->get('record_id') . PHP_EOL;
```

## `updateRecord(string $domain, string $recordId, DnsRecord $record)`

### Required

- `domain`
- `recordId`
- `record`

### Example

```php
$client->dns()->updateRecord(
    'example.com',
    'abc123',
    new DnsRecord([
        'rrhost' => 'www',
        'rrvalue' => '203.0.113.20',
        'rrtype' => 'A',
        'rrttl' => 3600,
    ])
);
```

## `deleteRecord(string $domain, string $recordId)`

### Required

- `domain`
- `recordId`

### Example

```php
$client->dns()->deleteRecord('example.com', 'abc123');
```

## Account Service

```php
$account = $client->account();
```

## `prices(bool $retailPrices = false, ?int $registrationDomains = null)`

Gets pricing data.

### Required

- none

### Optional

- `retailPrices`
- `registrationDomains`

### Example

```php
$response = $client->account()->prices();

$com = $response->get('com');
$net = $response->get('net');

print_r($com);
print_r($net);
```

## `fundsBalance()`

Gets account funds balance using the operation name mapped by this package.

### Required

- none

### Example

```php
$response = $client->account()->fundsBalance();
print_r($response->data());
```

## `addFunds(float $amount, ?string $paymentId = null)`

### Required

- `amount`

### Optional

- `paymentId`

### Example

```php
$client->account()->addFunds(50.00);
$client->account()->addFunds(50.00, '12345');
```

## `listOrders(?int $page = null)`

### Required

- none

### Optional

- `page`

### Example

```php
$response = $client->account()->listOrders();
$response = $client->account()->listOrders(2);
```

## `orderDetails(int|string $orderNumber)`

### Required

- `orderNumber`

### Example

```php
$response = $client->account()->orderDetails(123456);
print_r($response->data());
```

## Full Example

This example shows a realistic flow:

```php
<?php

declare(strict_types=1);

use Xililo\Namesilo\Exception\ApiException;
use Xililo\Namesilo\NamesiloClient;
use Xililo\Namesilo\ValueObject\ContactProfile;
use Xililo\Namesilo\ValueObject\DnsRecord;
use Xililo\Namesilo\ValueObject\DomainRegistration;

$client = new NamesiloClient('your-api-key');

try {
    $availability = $client->domains()->checkAvailability('example.com');

    print_r($availability->data());

    $contact = new ContactProfile([
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

    $contactResponse = $client->contacts()->add($contact);
    $contactId = $contactResponse->get('contact_id');

    $registration = new DomainRegistration([
        'private' => 1,
        'auto_renew' => 1,
        'contact_id' => $contactId,
    ]);

    $registerResponse = $client->domains()->register('example.com', 1, $registration);

    echo $registerResponse->get('message') . PHP_EOL;

    $dnsResponse = $client->dns()->addRecord(
        'example.com',
        new DnsRecord([
            'rrtype' => 'A',
            'rrhost' => 'www',
            'rrvalue' => '203.0.113.10',
            'rrttl' => 3600,
        ])
    );

    echo 'Created DNS record: ' . $dnsResponse->get('record_id') . PHP_EOL;
} catch (ApiException $exception) {
    echo 'API error: ' . $exception->getMessage() . PHP_EOL;
}
```

## Notes

- PHP `use` statements use backslashes:

```php
use Xililo\Namesilo\NamesiloClient;
```

- Composer package names use slashes:

```bash
composer require xililo/namesilo
```

- Some less common NameSilo operation names in this package follow NameSilo's official naming pattern and support index naming. The main structure is correct, but you should test the less common endpoints against a real account before relying on them in production.
