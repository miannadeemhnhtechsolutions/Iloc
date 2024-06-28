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
        Schema::create('new_subscription_plans', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('user_id');
            $table->date('start_date');
            $table->date('expiry_date');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->unsignedBigInteger('new_plan_id');
            $table->foreign('new_plan_id')->references('id')->on('new_plans')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status');
            $table->string('email')->nullable();
            $table->string('transaction_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_subscription_plans');
    }
};
