<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->foreignUuid('article_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at');
            $table->primary(['article_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('article_likes'); }
};
