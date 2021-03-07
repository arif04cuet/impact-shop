<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamMembers7 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->integer('order')->nullable()->change();
            $table->boolean('is_active')->nullable()->change();
            $table->integer('sort_order')->nullable()->change();
            $table->string('image', 191)->nullable()->change();
            $table->string('email', 191)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->integer('order')->nullable(false)->change();
            $table->boolean('is_active')->nullable(false)->change();
            $table->integer('sort_order')->nullable(false)->change();
            $table->string('image', 191)->nullable(false)->change();
            $table->string('email', 191)->nullable(false)->change();
        });
    }
}
