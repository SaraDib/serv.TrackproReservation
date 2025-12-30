<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_REPLIED = 'replied';
    const STATUS_ARCHIVED = 'archived';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NEW => 'Nouveau',
            self::STATUS_READ => 'Lu',
            self::STATUS_REPLIED => 'Répondu',
            self::STATUS_ARCHIVED => 'Archivé',
        ];
    }

    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? $this->status;
    }
}