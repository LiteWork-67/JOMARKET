<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Record an audit log entry for administrative or seller actions.
     */
    public static function log(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
