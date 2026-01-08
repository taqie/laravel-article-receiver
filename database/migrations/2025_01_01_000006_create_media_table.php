<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $mediaTable = (string) config('article-receiver.tables.media', 'ar_media');
        $articlesTable = (string) config('article-receiver.tables.article', 'ar_articles');

        Schema::create($mediaTable, function (Blueprint $table) use ($articlesTable): void {
            $table->id();
            $table->foreignId('article_id')->nullable()->constrained($articlesTable)->nullOnDelete();
            $table->string('filename');
            $table->string('path', 500);
            $table->string('disk');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('alt_text', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $mediaTable = (string) config('article-receiver.tables.media', 'ar_media');

        Schema::dropIfExists($mediaTable);
    }
};
