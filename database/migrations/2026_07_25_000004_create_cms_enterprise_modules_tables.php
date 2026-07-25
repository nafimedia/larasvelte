<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Menus Table
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('location')->default('navbar'); // navbar, footer, sidebar
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Menu Items Table
        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
                $table->string('title');
                $table->string('url')->nullable();
                $table->string('type')->default('custom'); // page, post, category, custom
                $table->integer('order')->default(0);
                $table->string('target')->default('_self'); // _self, _blank
                $table->timestamps();
            });
        }

        // 3. Media Files Table
        if (!Schema::hasTable('media_files')) {
            Schema::create('media_files', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('original_name');
                $table->string('path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->string('alt_text')->nullable();
                $table->text('caption')->nullable();
                $table->timestamps();
            });
        }

        // 4. Dynamic Forms Table
        if (!Schema::hasTable('cms_forms')) {
            Schema::create('cms_forms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('fields')->nullable();
                $table->string('submit_button_text')->default('Kirim Pesan');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. Form Submissions Table
        if (!Schema::hasTable('form_submissions')) {
            Schema::create('form_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_form_id')->constrained('cms_forms')->onDelete('cascade');
                $table->json('data');
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 6. Redirect Rules Table
        if (!Schema::hasTable('redirect_rules')) {
            Schema::create('redirect_rules', function (Blueprint $table) {
                $table->id();
                $table->string('old_url')->unique();
                $table->string('new_url');
                $table->integer('status_code')->default(301); // 301 or 302
                $table->unsignedBigInteger('hits')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('redirect_rules');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('cms_forms');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
