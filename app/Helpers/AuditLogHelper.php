<?php

use App\Services\AuditLogService;

if (!function_exists('logActivity')) {
    /**
     * Log a custom activity/audit event
     *
     * @param string $action
     * @param string $modelType
     * @param int|null $modelId
     * @param array $additionalData
     * @param string $level
     * @return void
     */
    function logActivity(
        string $action,
        string $modelType,
        ?int $modelId = null,
        array $additionalData = [],
        string $level = 'info'
    ): void {
        AuditLogService::logCustom(
            $action,
            "Activity: {$action}",
            $modelType,
            $level,
            array_merge($additionalData, [
                'model_id' => $modelId,
                'user_id' => auth()->id(),
            ])
        );
    }
}
