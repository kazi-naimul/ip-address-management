<?php

namespace App\Services\IpAddress;

use App\Library\Response\ResponseBuilder;
use App\Models\IpAddress;
use App\Models\User;
use App\Repositories\IpAddressRepository;
use App\Services\AuditLog\AuditLogService;

readonly class IpAddressService
{
    public function __construct(private IpAddressRepository $ipAddressRepository)
    {
    }

    public function getAll(): array
    {
        $ips = $this->ipAddressRepository->getAllWithCreator();

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('IP addresses retrieved successfully')
            ->data($ips)
            ->build();
    }

    public function create(array $data, User $user): array
    {
        $data['created_by'] = $user->id;
        $ipAddress = $this->ipAddressRepository->create($data);

        AuditLogService::record(
            $user,
            'ip_created',
            IpAddress::class,
            $ipAddress->id,
            $ipAddress->toArray()
        );

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(201)
            ->message('IP address created successfully')
            ->data($ipAddress)
            ->build();
    }

    public function update(IpAddress $ipAddress, array $data, User $user): array
    {
        $original = $ipAddress->toArray();

        $this->ipAddressRepository->update($ipAddress, ['label' => $data['label']]);

        AuditLogService::record(
            $user,
            'ip_updated',
            IpAddress::class,
            $ipAddress->id,
            ['before' => $original, 'after' => $ipAddress->toArray()]
        );

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('IP address updated successfully')
            ->data($ipAddress)
            ->build();
    }

    public function updateById(int $id, array $data, User $user): array
    {
        $ipAddress = $this->ipAddressRepository->findById($id);

        if (!$ipAddress) {
            return ResponseBuilder::getInstance()
                ->status(false)
                ->code(404)
                ->message('IP address not found')
                ->build();
        }

        return $this->update($ipAddress, $data, $user);
    }

    public function findById(int $id): ?IpAddress
    {
        return $this->ipAddressRepository->findById($id);
    }

    public function getById(int $id): array
    {
        $ipAddress = $this->ipAddressRepository->findById($id);

        if (!$ipAddress) {
            return ResponseBuilder::getInstance()
                ->status(false)
                ->code(404)
                ->message('IP address not found')
                ->build();
        }

        $ipAddress->load('creator');

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('IP address retrieved successfully')
            ->data($ipAddress)
            ->build();
    }
}