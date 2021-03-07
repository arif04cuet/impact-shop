<?php namespace Arstech\Webshop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration104 extends Migration
{
    public function up()
    {
         Schema::table('offline_mall_categories', function($table)
         {
         
             $table->text('second_description')->nullable();
         }
         
         );
    }

    public function down()
    {
        Schema::table('offline_mall_categories', function($table) {
            
                $table->dropColumn('second_description');
                
        });
    }
}