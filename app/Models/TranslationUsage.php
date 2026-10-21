<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['provider', 'month', 'characters', 'calls'])]
class TranslationUsage extends Model
{
    protected $table = 'translation_usage';
}
