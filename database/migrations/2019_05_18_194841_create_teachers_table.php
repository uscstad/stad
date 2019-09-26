<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('specialty')->nullable();
            $table->string('profession')->nullable();
            $table->text('training')->nullable();
            $table->text('scale')->nullable();
            $table->boolean('status')->default('1');
            $table->integer('deleted')->default('0');
            $table->integer('users_id')->unsigned(); 
            $table->integer('admins_id')->unsigned(); 
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admins_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
}
