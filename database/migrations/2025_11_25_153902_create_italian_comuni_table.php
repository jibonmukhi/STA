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
        Schema::create('italian_comuni', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100); // Municipality name
            $table->string('regione', 50); // Region name
            $table->string('provincia', 100)->nullable(); // Province subdivision name
            $table->string('sigla_provincia', 2)->nullable(); // Province abbreviation (TO, MI, etc.)
            $table->string('codice_catastale', 4)->unique(); // Cadastral code (used in CF)
            $table->timestamps();

            // Indexes for faster lookups
            $table->index('codice_catastale');
            $table->index('nome');
            $table->index('sigla_provincia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('italian_comuni');
    }
};
