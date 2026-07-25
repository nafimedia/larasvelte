<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'type',
        'name',
        'title',
        'subtitle',
        'description',
        'content',
        'settings',
        'order',
        'is_active',
        'status',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function versions()
    {
        return $this->hasMany(LandingSectionVersion::class);
    }
}
