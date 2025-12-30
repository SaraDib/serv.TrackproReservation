<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderSetting extends Model
{
    protected $fillable = [
        'logo_text',
        'logo_image',
        'navigation_items',
        'cta_button_text',
        'cta_button_link',
        'is_active'
    ];

    protected $casts = [
        'navigation_items' => 'array',
        'is_active' => 'boolean'
    ];
}
