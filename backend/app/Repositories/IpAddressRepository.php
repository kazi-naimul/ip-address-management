<?php

namespace App\Repositories;

use App\Models\IpAddress;

class IpAddressRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(new IpAddress());
    }

    public function getAllWithCreator()
    {
        return $this->model->with('creator')->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data): IpAddress
    {
        return $this->model->create($data);
    }

    public function update(IpAddress $ipAddress, array $data): bool
    {
        return $ipAddress->update($data);
    }

    public function findById(int $id): ?IpAddress
    {
        return $this->model->find($id);
    }
}