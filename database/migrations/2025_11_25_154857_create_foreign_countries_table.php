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
        Schema::create('foreign_countries', function (Blueprint $table) {
            $table->id();
            $table->string('nome_italiano', 100); // Italian name
            $table->string('nome_inglese', 100); // English name
            $table->string('codice_catastale', 4)->unique(); // Cadastral code for CF (Z followed by 3 digits)
            $table->string('codice_iso_alpha2', 2)->nullable(); // ISO 3166-1 alpha-2 code
            $table->string('codice_iso_alpha3', 3)->nullable(); // ISO 3166-1 alpha-3 code
            $table->timestamps();

            // Indexes for faster lookups
            $table->index('codice_catastale');
            $table->index('nome_italiano');
            $table->index('nome_inglese');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foreign_countries');
    }
};
