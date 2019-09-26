<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProloguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prologues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('start_date');
            $table->string('final_date');
            $table->text('justifications');
            $table->boolean('archive')->default('0');
            $table->text('archiveName')->nullable();
            $table->enum('status',['processing','finish'])->default('processing');
            $table->enum('accepted',['pending','yes','no'])->default('pending');
            $table->integer('tasks_contents_id')->unsigned()->nullable(); 
            $table->timestamps();

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
        Schema::dropIfExists('prologues');
    }
}
