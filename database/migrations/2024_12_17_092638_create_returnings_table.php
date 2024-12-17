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
        Schema::create('returnings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('returner_name');
            $table->string('code_item')->index('code_item');
            $table->string('returned_item');
            $table->bigInteger('amount');
            $table->string('return_date');
            $table->enum('status', ['Return', 'Borrow']);
            $table->timestamps();
            $table->bigInteger('user_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returnings');
    }
};
