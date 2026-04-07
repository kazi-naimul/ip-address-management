<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIpAddressRequest;
use App\Http\Requests\UpdateIpAddressRequest;
use App\Services\IpAddress\IpAddressService;

class IpAddressController extends Controller
{
    public function __construct(private IpAddressService $ipAddressService)
    {
    }

    public function index()
    {
        $response = $this->ipAddressService->getAll();

        return response()->json($response, $response['code']);
    }

    public function store(StoreIpAddressRequest $request)
    {
        $data = $request->validated();
        $response = $this->ipAddressService->create($data, $request->user());

        return response()->json($response, $response['code']);
    }

    public function update(UpdateIpAddressRequest $request, $id)
    {
        $response = $this->ipAddressService->updateById($id, $request->validated(), $request->user());

        return response()->json($response, $response['code']);
    }

    public function show($id)
    {
        $response = $this->ipAddressService->getById($id);

        return response()->json($response, $response['code']);
    }
}
