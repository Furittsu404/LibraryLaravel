<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lname')->nullable();
            $table->string('fname')->nullable();
            $table->string('mname')->nullable();
            $table->string('email')->nullable();
            $table->string('phonenumber')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->string('address')->nullable();
            $table->string('course')->nullable();
            $table->string('section')->nullable();
            $table->string('barcode');
            $table->enum('user_status', ['inside', 'outside'])->default('inside');
            $table->enum('user_type', ['student', 'visitor', 'faculty', 'staff'])->nullable();
            $table->enum('account_status', ['active', 'inactive'])->default('active');
            $table->date('expiration_date')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
