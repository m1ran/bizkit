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
        Schema::table('customers', function (Blueprint $table) {
            $table->index(['team_id', 'first_name'], 'idx_team_first_name');
            $table->index(['team_id', 'last_name'], 'idx_team_last_name');
            $table->index(['team_id', 'phone'], 'idx_team_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_team_first_name');
            $table->dropIndex('idx_team_last_name');
            $table->dropIndex('idx_team_phone');
        });
    }
};
