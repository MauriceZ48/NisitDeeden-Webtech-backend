<?php

namespace App\Models;

use App\Enums\RoundStatus;
use App\Enums\Semester;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ApplicationRound extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year',
        'semester',
        'status',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'semester' => Semester::class,
        'status'   => RoundStatus::class,
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

// In ApplicationRound Model
    public function scopeActive($query)
    {
        return $query->where('status', RoundStatus::OPEN)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());
    }

    public function isAcceptingSubmissions(): bool
    {
        return $this->status === RoundStatus::OPEN && now()->between($this->start_at, $this->end_at);
    }

    public function getDaysLeftAttribute(): int
    {
        if (now()->gt($this->end_time)) {
            return 0;
        }

        return (int) now()->diffInDays($this->end_time);
    }
}
