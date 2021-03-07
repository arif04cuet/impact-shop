<?php

namespace Arstech\Profile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateArstechProfile extends Migration
{
    public function up()
    {
        Schema::create('arstech_profile_profile', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('company', 200)->nullable();
            $table->string('department', 200)->nullable();
            $table->text('address')->nullable();
            $table->string('zip', 100)->nullable();
            $table->string('city', 200)->nullable();
            $table->string('invoice_email', 100)->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('arstech_profile_profile');
    }
}
