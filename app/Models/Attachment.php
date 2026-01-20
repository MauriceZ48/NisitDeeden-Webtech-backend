<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'application_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
