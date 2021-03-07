<?php namespace Arstech\Webshop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteArstechWebshopLocationProduct extends Migration
{
    public function up()
    {
        Schema::dropIfExists('arstech_webshop_location_product');
    }
    
    public function down()
    {
        Schema::create('arstech_webshop_location_product', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('location_id');
            $table->integer('product_id');
        });
    }
}
