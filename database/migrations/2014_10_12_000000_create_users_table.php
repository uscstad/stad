<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type_doc', ['pasaporte','ti','cc'])->default('cc');
            $table->string('doc');
            $table->string('name');
            $table->string('lastname');
            $table->string('user');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address');
            $table->string('mobile');
            $table->string('phone');
            $table->boolean('status')->default('1');
            $table->integer('deleted')->default('0');
            $table->enum('type', ['admins','coordinators','teachers'])->default('admins');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
