<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'company_name',
        'description',
        'logo_image',
        'quick_links',
        'services_links',
        'social_links',
        'copyright_text',
        'privacy_policy_url',
        'terms_of_service_url',
        'newsletter_enabled',
        'newsletter_title',
        'newsletter_description',
        'background_color',
        'text_color',
        'email',
        'phone',
        'address',
        'is_active'
    ];

    protected $casts = [
        'quick_links' => 'array',
        'services_links' => 'array',
        'social_links' => 'array',
        'is_active' => 'boolean',
        'newsletter_enabled' => 'boolean',
    ];
}
