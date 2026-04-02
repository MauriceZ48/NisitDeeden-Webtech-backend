<?php

namespace App\Models;

use App\Enums\Domain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active',
        'domain',
    ];

    protected $casts = [
        'domain' => Domain::class,
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class);
    }


    public function hasApplications(): bool
    {
        return $this->applications()->exists();
    }

    public function countApplications(): int
    {
        return $this->applications()->count();
    }

    public function isGlobal(): bool
    {
        return $this->domain == Domain::ALL;
    }

}
