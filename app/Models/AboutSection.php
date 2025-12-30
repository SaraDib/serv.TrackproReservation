<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $table = 'about_section';
    
    protected $fillable = [
        'title',
        'description',
        'image',
        'features',
        'flip_prefix',
        'flip_suffix',
        'flip_lines',
        'button_text',
        'button_link',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'flip_lines' => 'array',
        'is_active' => 'boolean'
    ];
}
