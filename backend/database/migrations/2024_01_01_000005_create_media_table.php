<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->unsignedBigInteger('size'); // bytes
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->foreignUuid('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->nullableMorphs('mediable'); // relation polymorphique
            $table->timestamps();

            $table->index('uploaded_by');
        });
    }
    public function down(): void { Schema::dropIfExists('media'); }
};
