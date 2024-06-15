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
        Schema::create('initiative_two_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
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

            // Former Debutante fields
            $table->string('debutante_name_at_presentation')->nullable();
            $table->string('debutante_escort_name')->nullable();
            $table->year('debutante_year_presented')->nullable();
            $table->string('debutante_sponsoring_organization')->nullable();
            $table->string('debutante_city')->nullable();
            $table->string('debutante_state')->nullable();

            // Former Beau fields
            $table->string('beau_name_at_presentation')->nullable();
            $table->string('beau_escort_name')->nullable();
            $table->year('beau_year_presented')->nullable();
            $table->string('beau_sponsoring_organization')->nullable();
            $table->string('beau_city')->nullable();
            $table->string('beau_state')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initiative_two_forms');
    }
};
