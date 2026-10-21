<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    private const REDACTED = ['password', 'remember_token', 'token', 'aadhaar_last4', 'aadhaar_number', 'aadhaar_hash', 'id_document'];

    public function log(string $action, ?Model $model = null, array $old = [], array $new = []): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'auditable_type' => $model ? $model::class : null,
                'auditable_id' => $model?->getKey(),
                'old_values' => $this->clean($old) ?: null,
                'new_values' => $this->clean($new) ?: null,
                'ip' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Audit log failed', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }

    private function clean(array $values): array
    {
        foreach ($values as $key => $value) {
            if (in_array($key, self::REDACTED, true) || str_contains((string) $key, 'password') || str_contains((string) $key, 'secret')) {
                $values[$key] = '[redacted]';
            }
        }

        return $values;
    }
}
