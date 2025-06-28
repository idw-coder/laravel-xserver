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
            $table->string('admin_id', 4)->nullable()->comment('4桁の管理者ID');
            $table->enum('qualification', ['qualified', 'unqualified'])->default('unqualified')->comment('資格の有無');
            $table->enum('role', ['general', 'sv', 'sl', 'manager'])->default('general')->comment('ユーザー権限');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_id', 'qualification', 'role']);
        });
    }
};
