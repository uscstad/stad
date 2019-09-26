<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->default('1');
            $table->integer('deleted')->default('0');
            $table->integer('admins_id')->unsigned(); 
            $table->integer('school_years_id')->unsigned()->nullable(); 
            $table->integer('teaching_periods_id')->unsigned()->nullable(); 
            $table->timestamps();

            $table->foreign('admins_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('school_years_id')->references('id')->on('school_years')->onDelete('cascade');
            $table->foreign('teaching_periods_id')->references('id')->on('teaching_periods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configs');
    }
}
