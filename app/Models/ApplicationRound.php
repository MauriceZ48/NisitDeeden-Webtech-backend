<?php

namespace App\Models;

use App\Enums\Domain;
use App\Enums\RoundStatus;
use App\Enums\Semester;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationRound extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year',
        'semester',
        'status',
        'start_time',
        'end_time',
        'domain'
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'semester' => Semester::class,
        'status'   => RoundStatus::class,
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'domain'    => Domain::class
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
// In ApplicationRound Model
    public function scopeActive($query)
    {
        return $query->where('status', RoundStatus::OPEN);

    }

    public function isAcceptingSubmissions(): bool
    {
        return $this->status === RoundStatus::OPEN &&
            now()->between($this->start_time, $this->end_time);
    }

    public function getDaysLeftAttribute(): int
    {
        if (now()->gt($this->end_time)) {
            return 0;
        }

        return (int) now()->diffInDays($this->end_time);
    }

    public function countApplications(): int
    {
        return $this->applications()->count();
    }

    public function getThaiAcademicYearAttribute(): string
    {
        return (string) ($this->academic_year + 543);
    }
}
