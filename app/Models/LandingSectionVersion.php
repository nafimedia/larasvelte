<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSectionVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_section_id',
        'version_name',
        'content',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];

    public function section()
    {
        return $this->belongsTo(LandingSection::class, 'landing_section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
