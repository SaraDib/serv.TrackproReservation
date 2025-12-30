<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmissionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_submission_id',
        'direction', // incoming | outgoing
        'subject',
        'message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(ContactSubmission::class, 'contact_submission_id');
    }
}