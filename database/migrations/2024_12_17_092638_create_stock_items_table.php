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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('items');
            $table->string('code', 50)->nullable()->default('DEFAULT')->unique('code');
            $table->integer('stock');
            $table->string('unit', 50)->nullable();
            $table->enum('condition', ['Good', 'Broken']);
            $table->string('location')->nullable()->default('UNKNOWN');
            $table->string('category');
            $table->string('image')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->enum('status', ['Borrow', 'Available'])->nullable()->default('Available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
