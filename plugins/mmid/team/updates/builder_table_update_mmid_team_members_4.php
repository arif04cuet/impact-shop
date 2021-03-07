<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamMembers4 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->integer('sort_order');
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
