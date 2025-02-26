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
        Schema::create('new_plans', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('slug');
            //$table->string('stripe_plan');
            $table->integer('price');
            $table->string('description');
            $table->string('interval');
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_plans');
    }
};
