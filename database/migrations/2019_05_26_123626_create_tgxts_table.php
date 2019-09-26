<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTgxtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcxts', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->default('1');
            $table->integer('deleted')->default('0');
            $table->integer('teachers_id')->unsigned(); 
            $table->integer('tasks_contents_id')->unsigned(); 
            $table->timestamps();

            $table->foreign('teachers_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('tasks_contents_id')->references('id')->on('tasks_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tcxts');
    }
}
