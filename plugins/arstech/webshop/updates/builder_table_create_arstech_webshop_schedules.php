<?php namespace Arstech\Webshop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateArstechWebshopSchedules extends Migration
{
    public function up()
    {
        Schema::create('arstech_webshop_schedules', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('start_date');
            $table->integer('location_id')->nullable();
            $table->string('start_time', 100);
            $table->string('end_time', 100);
            $table->boolean('is_fully_booked')->default(0);
            $table->integer('course_id')->nullable();
            $table->smallInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('arstech_webshop_schedules');
    }
}
