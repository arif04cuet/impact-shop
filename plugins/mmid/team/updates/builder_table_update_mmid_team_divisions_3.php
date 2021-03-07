<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamDivisions3 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_divisions', function($table)
        {
            $table->boolean('is_active');
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_divisions', function($table)
        {
            $table->dropColumn('is_active');
        });
    }
}
