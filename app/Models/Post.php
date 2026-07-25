<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'gallery',
        'category_id',
        'author_id',
        'reading_time',
        'is_sticky',
        'is_featured',
        'allow_comment',
        'preview_token',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'keywords',
        'canonical_url',
        'og_image',
        'view_count',
        'manual_related_ids',
        'preview_token_expires_at',
    ];

    public function ensurePreviewToken(): string
    {
        if (empty($this->preview_token)) {
            $this->preview_token = \Illuminate\Support\Str::random(32);
            $this->preview_token_expires_at = now()->addDays(7);
            $this->save();
        }
        return $this->preview_token;
    }

    protected $casts = [
        'gallery' => 'array',
        'manual_related_ids' => 'array',
        'is_sticky' => 'boolean',
        'is_featured' => 'boolean',
        'allow_comment' => 'boolean',
        'reading_time' => 'integer',
        'view_count' => 'integer',
        'published_at' => 'datetime',
        'preview_token_expires_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function revisions()
    {
        return $this->hasMany(PostRevision::class)->latest();
    }

    public function slugRedirects()
    {
        return $this->hasMany(PostSlugRedirect::class);
    }
}
