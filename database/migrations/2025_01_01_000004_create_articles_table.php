<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $articlesTable = (string) config('article-receiver.tables.article', 'ar_articles');
        $authorsTable = (string) config('article-receiver.tables.author', 'ar_authors');
        $categoriesTable = (string) config('article-receiver.tables.category', 'ar_categories');

        Schema::create($articlesTable, function (Blueprint $table) use ($authorsTable, $categoriesTable): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('lead');
            $table->string('meta_description', 160);
            $table->longText('body');
            $table->string('status')->default('draft');
            $table->foreignId('author_id')->nullable()->constrained($authorsTable)->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained($categoriesTable)->nullOnDelete();
            $table->string('featured_image_url', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->timestamps();

            $table->index('status');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        $articlesTable = (string) config('article-receiver.tables.article', 'ar_articles');

        Schema::dropIfExists($articlesTable);
    }
};
