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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('customer_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('patronymic_name')->nullable()->after('last_name');
            $table->string('email')->nullable()->after('patronymic_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete()->after('city');
            $table->string('zip')->nullable()->after('state_id');

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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'patronymic_name', 'email', 'phone', 'address', 'city', 'state_id', 'zip']);

            $table->dropIndex('idx_team_first_name');
            $table->dropIndex('idx_team_last_name');
            $table->dropIndex('idx_team_phone');
        });
    }
};
