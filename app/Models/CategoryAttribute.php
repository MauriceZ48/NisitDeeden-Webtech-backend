<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryAttribute extends Model
{
    protected $fillable = [
        'application_category_id',
        'label',
        'type',
        'is_required'
    ];

    /**
     * Get the category that owns this attribute.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ApplicationCategory::class);
    }

    /**
     * Get the values submitted for this specific attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(ApplicationAttributeValue::class);
    }
}
