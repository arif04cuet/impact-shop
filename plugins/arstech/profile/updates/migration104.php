<?php namespace Arstech\Profile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration104 extends Migration
{
    public function up()
    {
        if (Schema::hasColumns('users', ['phone'])) {
            return;
        }

        Schema::table('users', function ($table) {

            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users') && Schema::hasColumns('users', ['phone'])) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['phone']);
            });
        }
    }
}