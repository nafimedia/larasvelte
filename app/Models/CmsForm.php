<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'fields',
        'submit_button_text',
        'is_active',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
    ];

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class)->latest();
    }
}
