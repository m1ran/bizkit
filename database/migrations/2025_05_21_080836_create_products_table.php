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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sku');
            $table->string('product_photo_path')->nullable();
            $table->text('description')->nullable();
            // price & cost
            $table->decimal('price', 10, 2)
                ->comment('Selling price');
            $table->decimal('cost', 10, 2)
                ->nullable()
                ->comment('Your cost or landed cost');
            // inventory
            $table->integer('quantity')->default(0);
            $table->string('unit')->nullable() // e.g. “pcs”, “kg”, etc.
                ->comment('Unit of measure');
            // optional fields
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('product_categories')
                ->nullOnDelete();
            $table->string('barcode')->nullable();
            $table->boolean('active')->default(true);
            // timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
