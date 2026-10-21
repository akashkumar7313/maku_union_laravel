<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['grievance_id', 'path', 'original_name', 'mime', 'size', 'uploaded_by'])]
class GrievanceDocument extends Model
{
    public function grievance(): BelongsTo
    {
        return $this->belongsTo(Grievance::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
