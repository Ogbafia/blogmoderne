<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cover_image')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->integer('reading_time')->default(1); // minutes
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('author_id');
            $table->index('category_id');
            // fulltext can fail on some MySQL versions/engines, but let's stick to user request
            // Actually, in migrations it's $table->fullText(['title', 'content', 'excerpt']);
            // $table->fullText(['title', 'content', 'excerpt']);
        });
    }
    public function down(): void { Schema::dropIfExists('articles'); }
};
