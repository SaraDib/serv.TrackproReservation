<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'badge_icon',
        'badge_text',
        'stats',
        'actions',
        'features',
        'button_text',
        'button_link',
        'background_image',
        'background_color',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stats' => 'array',
        'actions' => 'array',
        'features' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
