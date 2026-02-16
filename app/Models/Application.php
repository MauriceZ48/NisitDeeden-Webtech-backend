<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'application_category_id',
        'status',
        'user_id',
        'application_round_id',
        'rejection_reason',
        'transcript_path',
    ];
    protected $casts = [
        'status' => ApplicationStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function applicationRound(): BelongsTo
    {
        return $this->belongsTo(ApplicationRound::class);
    }

    public function applicationCategory(): BelongsTo
    {
        return $this->belongsTo(ApplicationCategory::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ApplicationAttributeValue::class);
    }
}
