<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'banner_image',
        'parent_id',
        'template',
        'order',
        'status',
        'visibility',
        'password',
        'author_id',
        'published_at',
        'meta_title',
        'meta_description',
        'keywords',
        'canonical_url',
        'og_image',
        'custom_css',
        'custom_js',
        'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'order' => 'integer',
        'view_count' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
