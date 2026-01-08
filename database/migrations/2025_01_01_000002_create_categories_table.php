<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = (string) config('article-receiver.tables.category', 'ar_categories');

        Schema::create($tableName, function (Blueprint $table) use ($tableName): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained($tableName)
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tableName = (string) config('article-receiver.tables.category', 'ar_categories');

        Schema::dropIfExists($tableName);
    }
};
