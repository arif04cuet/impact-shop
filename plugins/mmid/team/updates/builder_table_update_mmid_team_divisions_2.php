<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamDivisions2 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_divisions', function($table)
        {
            $table->renameColumn('order', 'sort_order');
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_divisions', function($table)
        {
            $table->renameColumn('sort_order', 'order');
        });
    }
}
