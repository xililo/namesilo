# Xililo NameSilo PHP Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xililo/namesilo.svg)](https://packagist.org/packages/xililo/namesilo)
[![PHP Version](https://img.shields.io/packagist/php-v/xililo/namesilo.svg)](https://packagist.org/packages/xililo/namesilo)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A clean and comprehensive Composer package for the [NameSilo API](https://www.namesilo.com/api-reference).

This package provides a simple, object-oriented interface to interact with NameSilo's domain registration and management services, including domain operations, DNS management, transfers, and more.

Full documentation is available in [docs/USAGE.md](docs/USAGE.md).

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Services](#services)
  - [Domains](#domains)
  - [Transfers](#transfers)
  - [Contacts](#contacts)
  - [NameServers](#nameservers)
  - [DNS](#dns)
  - [Account](#account)
- [Responses](#responses)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Domain Operations**: Check availability, register, renew, transfer domains
- **DNS Management**: Add, update, delete DNS records
- **Contact Management**: Manage contact profiles for domain registration
- **Name Server Operations**: Register and manage custom name servers
- **Transfer Operations**: Handle domain transfers in and out
- **Account Management**: Check balances, view orders, add funds
- **JSON-first Response Parsing**: Consistent API responses
- **Flexible Transport Layer**: Easy to mock for testing
- **Exception-based Error Handling**: Clear error reporting
- **Full API Coverage**: Supports all major NameSilo API endpoints

## Requirements

- PHP 8.1 or higher
- cURL extension
- JSON extension
- A valid NameSilo API key

## Installation

Install via Composer:

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

// Check domain availability
$availability = $client->domains()->checkAvailability(['example.com', 'example.net']);

// Get domain information
$domainInfo = $client->domains()->info('example.com');

// Add a DNS record
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

$response->isSuccess();  // bool
$response->code();       // string (NameSilo reply code)
$response->detail();     // string (human-readable message)
$response->data();       // array (parsed response data)
$response->get('expires'); // mixed (access specific data key)
```

## Error Handling

By default, non-success NameSilo reply codes throw `Xililo\Namesilo\Exception\ApiException`. You can catch these exceptions to handle API errors gracefully:

```php
use Xililo\Namesilo\Exception\ApiException;

try {
    $client->domains()->register('example.com', 1);
} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage();
    echo "Reply Code: " . $e->getCode();
}
```

Other exceptions that may be thrown:
- `InvalidArgumentException`: When invalid arguments are provided
- `TransportException`: When HTTP transport fails
- `ResponseParsingException`: When API response cannot be parsed

## Testing

This package includes a comprehensive test suite. To run tests:

```bash
composer test
```

The package uses a mock transport layer for testing, allowing you to test your integration without making real API calls.

## Notes About Operation Mapping

Most operation names in this package were verified against NameSilo's official API reference and support index. A few names in the `NameServers` and `Account` areas are based on NameSilo's support article titles and their existing naming pattern.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
