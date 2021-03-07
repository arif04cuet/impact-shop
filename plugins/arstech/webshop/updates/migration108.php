<?php namespace Arstech\Webshop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration108 extends Migration
{
    public function up()
    {
         Schema::table('offline_mall_discounts', function($table)
         {
         
             $table->text('products')->nullable();
         }
         
         );
    }

    public function down()
    {
        Schema::table('offline_mall_discounts', function($table) {
            
                $table->dropColumn('products');
                
        });
    }
}