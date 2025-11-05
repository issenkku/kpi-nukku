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
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->string('variable_name');
            $table->string('label_name');
            $table->string('type', 50);
            $table->float('value', 5, 2)->nullable();
            
            $table->foreignId('indicator_id')
            ->constrained('indicators')
            ->cascadeOnDelete();
            
            $table->timestamps();
        });

        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->text('condition');
            $table->timestamps();

            $table->foreignId('indicator_id')
                ->constrained('indicators')
                ->cascadeOnDelete();
        });

        Schema::create('variable_formulas', function (Blueprint $table) {
            $table->foreignId('variable_id')
                ->constrained('variables')
                ->cascadeOnDelete();

            $table->foreignId('formula_id')
                ->constrained('formulas')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variable_formulas');
        Schema::dropIfExists('formulas');
        Schema::dropIfExists('variables');
    }
};
