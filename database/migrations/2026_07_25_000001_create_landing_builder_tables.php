<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Landing Sections Table
        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_id')->unique(); // Unique slug/key e.g., hero-1, features-1
            $table->string('type'); // hero, features, stats, testimonials, pricing, faq, cta, custom_html, contact, etc.
            $table->string('name'); // Admin display name
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->json('content')->nullable(); // Component items, text, buttons, cards, media
            $table->json('settings')->nullable(); // Background, padding, width, align, animation, responsive
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });

        // 2. Section Version History Table
        Schema::create('landing_section_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_section_id')->constrained('landing_sections')->onDelete('cascade');
            $table->string('version_name')->nullable();
            $table->json('content');
            $table->json('settings');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 3. Global Site & Theme Settings Table
        Schema::create('landing_site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->json('json_value')->nullable();
            $table->timestamps();
        });

        // 4. Media Library Table
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('alt_text')->nullable();
            $table->string('folder')->default('general');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 5. Custom Forms Table
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('fields'); // Form field builder schema
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Form Submissions Table
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_id')->constrained('custom_forms')->onDelete('cascade');
            $table->json('data');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('custom_forms');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('landing_site_settings');
        Schema::dropIfExists('landing_section_versions');
        Schema::dropIfExists('landing_sections');
    }
};
