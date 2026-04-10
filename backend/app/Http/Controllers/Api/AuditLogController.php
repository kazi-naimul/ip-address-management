<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditLog\AuditLogQueryService;

class AuditLogController extends Controller
{
    public function __construct(private AuditLogQueryService $auditLogQueryService)
    {
    }

    public function index()
    {
        $response = $this->auditLogQueryService->getAllAuditLogs();

        return response()->json($response, $response['code']);
    }

    public function getLoginAuditLogs()
    {
        $response = $this->auditLogQueryService->getLoginAuditLogs();

        return response()->json($response, $response['code']);
    }

    public function getUserLoginAuditLogs()
    {
        $response = $this->auditLogQueryService->getUserLoginAuditLogs(auth()->user()->id);

        return response()->json($response, $response['code']);
    }

    public function getIpAddressChangeHistory($id)
    {
        $response = $this->auditLogQueryService->getIpAddressChangeHistory($id);

        return response()->json($response, $response['code']);
    }
}
