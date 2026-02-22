<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAttributeValue extends Model
{
    protected $fillable = [
        'application_id',
        'category_attribute_id',
        'value'
    ];

    /**
     * Get the application this value belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the attribute definition this value is answering.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }
}
