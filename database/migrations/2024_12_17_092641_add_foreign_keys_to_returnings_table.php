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
        Schema::table('returnings', function (Blueprint $table) {
            $table->foreign(['code_item'], 'returnings_ibfk_1')->references(['code_item'])->on('borrowings')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returnings', function (Blueprint $table) {
            $table->dropForeign('returnings_ibfk_1');
        });
    }
};
