<?php namespace Arstech\Webshop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateArstechWebshopSchedules extends Migration
{
    public function up()
    {
        Schema::table('arstech_webshop_schedules', function($table)
        {
            $table->smallInteger('max_participants');
            $table->text('dates')->nullable();
            $table->dropColumn('start_date');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });
    }
    
    public function down()
    {
        Schema::table('arstech_webshop_schedules', function($table)
        {
            $table->dropColumn('max_participants');
            $table->dropColumn('dates');
            $table->date('start_date');
            $table->string('start_time', 100);
            $table->string('end_time', 100);
        });
    }
}
