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
        Schema::create('participant_female_forms', function (Blueprint $table) {
            $table->id();
            $table->string('debutante_name_at_presentation')->nullable();
            $table->string('debutante_escort_name')->nullable();
            $table->year('debutante_year_presented')->nullable();
            $table->string('debutante_sponsoring_organization')->nullable();
            $table->string('debutante_city')->nullable();
            $table->string('debutante_state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_female_forms');
    }
};
