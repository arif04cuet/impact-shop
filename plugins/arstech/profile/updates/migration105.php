<?php namespace Arstech\Profile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration105 extends Migration
{
     public function up()
    {
        if (Schema::hasColumns('users', ['ffp_number', 'seh_number', 'bsn_number'])) {
            return;
        }

        Schema::table('users', function ($table) {

            $table->string('ffp_number')->nullable();
            $table->string('seh_number')->nullable();
            $table->string('bsn_number')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users') && Schema::hasColumns('users', ['ffp_number', 'seh_number', 'bsn_number'])) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['ffp_number', 'seh_number', 'bsn_number']);
            });
        }
    }
}