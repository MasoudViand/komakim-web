<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('national_code');
            $table->string('home_phone_number');
            $table->string('address');
            $table->dateTime('birthday');
            $table->string('last_education');
            $table->string('availability_status');
            $table->string('marriage_status');
            $table->string('gender');
            $table->string('another_capability');
            $table->string('certificates');
            $table->string('experience');
            $table->string('field');
            $table->string('status');
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
        Schema::dropIfExists('worker_profiles');
    }
}
