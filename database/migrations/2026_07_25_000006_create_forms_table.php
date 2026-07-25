<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop legacy form tables if exists
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('cms_forms');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');

        // 1. Forms table
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('header_image')->nullable();
            $table->string('theme_color')->default('#6366F1');
            $table->boolean('is_accepting_responses')->default(true);
            $table->text('confirmation_message')->default('Terima kasih, tanggapan Anda telah berhasil disimpan.');
            $table->boolean('require_login')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Form fields table
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->string('type'); // text, paragraph, multiple_choice, checkboxes, dropdown, file, date, rating
            $table->string('label');
            $table->text('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->json('options')->nullable(); // ["Option 1", "Option 2"]
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Form submissions table
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('response_data');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');
    }
};
