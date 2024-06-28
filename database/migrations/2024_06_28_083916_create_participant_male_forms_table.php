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
        Schema::create('participant_male_forms', function (Blueprint $table) {
            $table->id();
            $table->string('beau_name_at_presentation')->nullable();
            $table->string('beau_escort_name')->nullable();
            $table->year('beau_year_presented')->nullable();
            $table->string('beau_sponsoring_organization')->nullable();
            $table->string('beau_city')->nullable();
            $table->string('beau_state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_male_forms');
    }
};
