<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAddresses extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('type')->default('normal');
            $table->string('name');
            $table->string('street');
            $table->integer('number');
            $table->string('district');
            $table->string('state');
            $table->string('city');
            $table->string('zip_code');
            $table->string('complement')->nullable();
            $table->string('reference')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_adresses');
    }
}
