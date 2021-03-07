<?php

namespace Arstech\Profile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration102 extends Migration
{

    public function up()
    {
        if (Schema::hasColumns('users', ['salution', 'initials', 'infix', 'address', 'zip', 'city'])) {
            return;
        }

        Schema::table('users', function ($table) {

            $table->string('salution')->nullable();
            $table->string('initials')->nullable();
            $table->string('infix')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users') && Schema::hasColumns('users', ['salution', 'initials', 'infix', 'address', 'zip', 'city'])) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['salution', 'initials', 'infix', 'address', 'zip', 'city']);
            });
        }
    }
}