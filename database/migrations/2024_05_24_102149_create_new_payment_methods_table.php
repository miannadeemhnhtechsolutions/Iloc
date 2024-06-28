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
        Schema::create('new_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('subscription_id');
            $table->string('public_key');
            $table->string('secret_key');
            $table->boolean('is_active')->default(0);
            $table->string('paymentId')->nullable();
            $table->string('PayerID')->nullable();
            $table->string('email')->nullable();
            $table->string('transaction_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('new_subscription_plans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_payment_methods');
    }
};
