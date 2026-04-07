<?php

namespace App\Services\AuditLog;

use App\Library\Response\ResponseBuilder;
use App\Models\IpAddress;
use App\Repositories\AuditLogRepository;
use Illuminate\Contracts\Pagination\Paginator;

readonly class AuditLogQueryService
{
    public function __construct(private AuditLogRepository $auditLogRepository)
    {
    }

    public function getAllAuditLogs($limit = 50): array
    {
        $logs = $this->auditLogRepository->getAllAuditLogs($limit);

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('Audit logs retrieved successfully')
            ->data($logs)
            ->build();
    }

    public function getLoginAuditLogs($limit = 50): array
    {
        $logs = $this->auditLogRepository->getLoginAuditLogs(null, $limit);

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('Login audit logs retrieved successfully')
            ->data($logs)
            ->build();
    }

    public function getUserLoginAuditLogs(int $userId, $limit = 50): array
    {
        $logs = $this->auditLogRepository->getLoginAuditLogs($userId, $limit);

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('User login history retrieved successfully')
            ->data($logs)
            ->build();
    }

    public function getIpAddressChangeHistory(int $id): array
    {
        $ipAddress = IpAddress::find($id);

        if (!$ipAddress) {
            return ResponseBuilder::getInstance()
                ->status(false)
                ->code(404)
                ->message('IP address not found')
                ->build();
        }

        $history = $this->auditLogRepository->getAuditLogsByModelId(IpAddress::class, $id);

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('IP address change history retrieved successfully')
            ->data([
                'ip_address' => $ipAddress,
                'changes' => $history
            ])
            ->build();
    }
}