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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('department', ['it', 'ac', 'sl', 'hr', 'mk'])
                ->nullable()
                ->after('role')
                ->comment('部署区分: it=情報システム課, ac=経理課, sl=成約課, hr=人事課, mk=マーケティング課');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
