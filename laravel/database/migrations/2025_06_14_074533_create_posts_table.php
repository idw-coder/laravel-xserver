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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // 投稿者
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 記事メタ
            $table->string('title', 255);
            $table->string('slug', 255)->unique();   // /posts/{slug} 用
            $table->text('excerpt')->nullable();     // 一覧用サマリ
            $table->longText('body');               // 本文 Markdown 等

            // 公開制御
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft')
                ->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->timestamps();      // created_at / updated_at
            $table->softDeletes();     // deleted_at ― ゴミ箱機能
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
