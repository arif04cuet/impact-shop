<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamMembers5 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->string('image');
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->dropColumn('image');
        });
    }
}
