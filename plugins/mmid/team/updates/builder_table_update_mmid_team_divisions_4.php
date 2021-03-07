<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamDivisions4 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_divisions', function($table)
        {
            $table->renameColumn('title', 'name');
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_divisions', function($table)
        {
            $table->renameColumn('name', 'title');
        });
    }
}
