<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksIndividualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['individuals', 'groups'])->nullable();
            $table->longtext('categories');
            $table->string('start_date');
            $table->string('final_date');
            $table->boolean('attachments')->default('1');
            $table->text('comments')->nullable(); 
            $table->enum('boards',['pending','inprocess','finish'])->default('pending');
            $table->text('answer')->nullable(); 
            $table->text('archiveName')->nullable(); 
            $table->enum('positive',['pending','yes','no'])->default('pending');
            $table->longtext('level_educations');
            $table->longtext('subjects');
            $table->text('detail'); 
            $table->text('recommendations')->nullable(); 
            $table->enum('status',['assigned','processing','pending','reprogrammed','canceled','unfulfilled','finishedtime','finished'])->default('assigned');
            $table->integer('jobs')->default('1');
            $table->text('jobs_comments')->nullable(); 
            $table->integer('deleted')->default('0');
            $table->integer('teachers_id')->unsigned(); 
            $table->integer('teaching_periods_id')->unsigned(); 
            $table->integer('tasks_id')->unsigned(); 
            $table->timestamps();

            $table->foreign('teachers_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('teaching_periods_id')->references('id')->on('teaching_periods')->onDelete('cascade');
            $table->foreign('tasks_id')->references('id')->on('tasks')->onDelete('cascade');       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks_contents');
    }
}
