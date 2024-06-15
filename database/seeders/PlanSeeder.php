<?php

namespace Database\Seeders;

use App\Models\NewPlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $table->unsignedBigInteger('user_id');
//        $table->string('name');
//        $table->string('slug');
//        //$table->string('stripe_plan');
//        $table->integer('price');
//        $table->string('description');
//        $table->string('interval');

        NewPlan::create([
            'user_id'=>1,
            'name' => 'Plan 1',
            'slug' => 'individual or businesses',
            'price'=>50,
            'description' => 'Individual or Businesses',
            'interval' => 'month',

        ]);
        NewPlan::create([
            'user_id'=>1,
            'name' => 'Plan 2',
            'slug' => 'cotillion organizations, clubs, groups and foundations',
            'price'=>100,
            'description' => 'Cotillion Organizations, Clubs, Groups and Foundations',
            'interval' => 'month',

        ]);
    }
}
