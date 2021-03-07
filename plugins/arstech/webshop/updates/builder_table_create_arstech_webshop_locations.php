<?php namespace Arstech\Webshop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateArstechWebshopLocations extends Migration
{
    public function up()
    {
        Schema::create('arstech_webshop_locations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 200);
            $table->text('address')->nullable();
            $table->string('zipcode', 100);
            $table->string('city', 100);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 10, 8);
            $table->boolean('status')->default(1);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('arstech_webshop_locations');
    }
}
