<?php

namespace App\Models;

use App\Enums\GrievanceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'case_no', 'member_id', 'name', 'phone', 'email', 'worker_category_id', 'subject',
    'description', 'status', 'priority', 'assigned_to', 'resolved_at', 'resolution_note', 'ip',
])]
class Grievance extends Model
{
    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WorkerCategory::class, 'worker_category_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(GrievanceUpdate::class)->latest();
    }

    public function publicUpdates(): HasMany
    {
        return $this->hasMany(GrievanceUpdate::class)->where('is_public', true)->latest();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(GrievanceDocument::class);
    }

    public function statusLabel(): string
    {
        return GrievanceStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function statusColor(): string
    {
        return GrievanceStatus::tryFrom($this->status)?->color() ?? 'secondary';
    }

    public function getRouteKeyName(): string
    {
        return 'case_no';
    }
}
