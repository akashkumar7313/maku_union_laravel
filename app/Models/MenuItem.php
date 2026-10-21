<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

#[Fillable(['parent_id', 'label', 'url', 'route_name', 'sort_order', 'is_active', 'opens_new_tab'])]
class MenuItem extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'opens_new_tab' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function href(): string
    {
        if ($this->route_name && Route::has($this->route_name)) {
            return route($this->route_name);
        }

        if ($this->url) {
            return str_starts_with($this->url, 'http') ? $this->url : url($this->url);
        }

        return '#';
    }
}
