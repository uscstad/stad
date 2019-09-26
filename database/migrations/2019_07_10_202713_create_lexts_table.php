<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lexts', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->default('1');
            $table->integer('deleted')->default('0');
            $table->integer('teachers_id')->unsigned(); 
            $table->integer('level_educations_id')->unsigned(); 
            $table->timestamps();

            $table->foreign('teachers_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('level_educations_id')->references('id')->on('level_educations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lexts');
    }
}
