<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Supprimez le cast 'variables' => 'array' car vous gérez manuellement la conversion

    /**
     * Get template by type
     */
    public static function getByType($type)
    {
        return self::where('type', $type)->where('is_active', true)->first();
    }

    /**
     * Replace variables in template content
     */
    public function renderContent($variables = [])
    {
        $content = $this->html_content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Replace variables in template subject
     */
    public function renderSubject($variables = [])
    {
        $subject = $this->subject;
        
        foreach ($variables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }
        
        return $subject;
    }

    /**
     * Accessor pour les variables - convertir JSON en tableau
     */
    public function getVariablesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Mutator pour les variables - convertir tableau en JSON
     */
    public function setVariablesAttribute($value)
    {
        $this->attributes['variables'] = is_array($value) ? json_encode($value) : $value;
    }
}