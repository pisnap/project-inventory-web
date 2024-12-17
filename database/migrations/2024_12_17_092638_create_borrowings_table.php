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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('borrower_name')->nullable();
            $table->string('code_item', 50)->nullable()->index('code_item');
            $table->string('borrowed_item')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('borrow_date')->nullable();
            $table->enum('status', ['Borrow', 'Return']);
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->bigInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
