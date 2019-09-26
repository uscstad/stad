<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->text('body');
            $table->longtext('senders');
            $table->boolean('status')->default('1');
            $table->integer('deleted')->default('0');
            $table->integer('tasks_contents_id')->unsigned()->nullable(); 
            $table->integer('users_id')->unsigned(); 
            $table->timestamps();

            $table->foreign('tasks_contents_id')->references('id')->on('tasks_contents')->onDelete('cascade');  
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages_contents');
    }
}
