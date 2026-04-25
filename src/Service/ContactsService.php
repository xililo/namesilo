<?php

declare(strict_types=1);

namespace Xililo\Namesilo\Service;

use Xililo\Namesilo\Response\ApiResponse;
use Xililo\Namesilo\ValueObject\ContactProfile;

final class ContactsService extends AbstractService
{
    public function list(int|string|null $contactId = null, ?int $offset = null): ApiResponse
    {
        return $this->call('contactList', array_filter([
            'contact_id' => $contactId,
            'offset' => $offset,
        ], static fn (mixed $value): bool => $value !== null));
    }

    public function add(ContactProfile $profile): ApiResponse
    {
        return $this->call('contactAdd', $profile->toArray());
    }

    public function update(int|string $contactId, ContactProfile $profile): ApiResponse
    {
        return $this->call('contactUpdate', array_merge([
            'contact_id' => $contactId,
        ], $profile->toArray()));
    }

    public function delete(int|string $contactId): ApiResponse
    {
        return $this->call('contactDelete', [
            'contact_id' => $contactId,
        ]);
    }

    public function associateWithDomain(string $domain, int|string $contactId): ApiResponse
    {
        return $this->call('contactDomainAssociate', [
            'domain' => $domain,
            'contact_id' => $contactId,
        ]);
    }
}
