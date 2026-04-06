<?php

namespace App\Services\AuditLog;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogService
{
    public static function record(User $user, string $action, ?string $modelType = null, ?int $modelId = null, array $details = [])
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
        ]);
    }
}
