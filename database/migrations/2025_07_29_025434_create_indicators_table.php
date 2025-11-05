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
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('max_score', 5, 2)->nullable();

            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
        });

        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('year', 5)->nullable();
            $table->string('code', 100);
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->text('condition')->nullable();
            $table->text('annotation')->nullable();
            $table->date('deadline');
            $table->integer('status')->default(0);
            $table->text('comment')->nullable();
            $table->float('score_acc', 5, 2)->nullable();
            $table->float('max_score', 5, 2)->nullable();
            $table->timestamps();

            $table->foreignId('categorie_id')->constrained('categories', 'id')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('standards');
    }
};
