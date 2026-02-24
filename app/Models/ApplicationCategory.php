<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active'
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function hasApplications(): bool
    {
        return $this->applications()->exists();
    }

    public function countApplications(): int
    {
        return $this->applications()->count();
    }

}
