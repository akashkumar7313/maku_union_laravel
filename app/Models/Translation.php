<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['locale', 'key', 'value', 'source', 'reviewed'])]
class Translation extends Model
{
    protected function casts(): array
    {
        return ['reviewed' => 'boolean'];
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('reviewed', false);
    }
}
