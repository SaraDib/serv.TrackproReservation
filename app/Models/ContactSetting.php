<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'email',
        'phone',
        'address',
        'whatsapp',
        'emergency_contact',
        'office_hours',
        'map_embed',
        'contact_form_enabled',
        'auto_reply_enabled',
        'auto_reply_message',
        'social_links',
        'form_settings',
        'is_active'
    ];

    protected $casts = [
        'social_links' => 'array',
        'form_settings' => 'array',
        'contact_form_enabled' => 'boolean',
        'auto_reply_enabled' => 'boolean',
        'is_active' => 'boolean'
    ];
}
