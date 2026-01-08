<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $pivotTable = (string) config('article-receiver.tables.article_tag', 'ar_article_tag');
        $articlesTable = (string) config('article-receiver.tables.article', 'ar_articles');
        $tagsTable = (string) config('article-receiver.tables.tag', 'ar_tags');

        Schema::create($pivotTable, function (Blueprint $table) use ($articlesTable, $tagsTable): void {
            $table->foreignId('article_id')->constrained($articlesTable)->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained($tagsTable)->cascadeOnDelete();

            $table->primary(['article_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        $pivotTable = (string) config('article-receiver.tables.article_tag', 'ar_article_tag');

        Schema::dropIfExists($pivotTable);
    }
};
