<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Post Revisions Table
        Schema::create('post_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        // Slug Redirects Table (301 Redirects when slug changes)
        Schema::create('post_slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->string('old_slug')->index();
            $table->timestamps();
        });

        // Add advanced columns to posts table
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'manual_related_ids')) {
                $table->json('manual_related_ids')->nullable();
            }
            if (!Schema::hasColumn('posts', 'preview_token_expires_at')) {
                $table->timestamp('preview_token_expires_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_slug_redirects');
        Schema::dropIfExists('post_revisions');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['manual_related_ids', 'preview_token_expires_at']);
        });
    }
};
