<?php

namespace App\Repositories;

use App\Models\AuditLog;

class AuditLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(new AuditLog());
    }

    public function create(array $data): ?AuditLog
    {
        return parent::create($data);
    }

    public function getAllAuditLogs($limit = 50)
    {
        return $this->model
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function getAuditLogsByAction(string $action, $limit = 50)
    {
        return $this->model
            ->where('action', $action)
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function getAuditLogsByModelId(string $modelType, int $modelId)
    {
        return $this->model
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLoginAuditLogs(int $userId = null, $limit = 50)
    {
        $query = $this->model->where('action', 'user_login');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }
}