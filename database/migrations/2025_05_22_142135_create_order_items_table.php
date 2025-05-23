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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1)->comment('Number of units');
            $table->decimal('unit_cost', 10, 2)->comment('Cost per unit at time of order');
            $table->decimal('unit_price', 10, 2)->comment('Price per unit at time of order');
            // Computed if supported, otherwise calculate in code
            $table->decimal('line_cost', 12, 2)
                ->storedAs('quantity * unit_cost')
                ->comment('Computed quantity x unit_cost');
            $table->decimal('line_price', 12, 2)
                ->storedAs('quantity * unit_price')
                ->comment('Computed quantity x unit_price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
