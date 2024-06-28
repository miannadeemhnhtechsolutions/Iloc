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
        Schema::create('participant_organization_forms', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name');
            $table->string('organization_city');
            $table->string('organization_state');
            $table->string('organization_email')->nullable();
            $table->string('organization_website')->nullable();
            $table->year('organization_established_year');
            $table->year('organization_year')->nullable();
            $table->string('organization_age')->nullable();
            $table->boolean('organization_is_active');
            $table->enum('organization_presentation_type', ['debutantes', 'beaus', 'both','males','females','other']);
            $table->enum('organization_presentation_frequency', ['annually', 'biannually']);
            $table->enum('organization_participation_method', ['invite', 'referral', 'open']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_organization_forms');
    }
};
