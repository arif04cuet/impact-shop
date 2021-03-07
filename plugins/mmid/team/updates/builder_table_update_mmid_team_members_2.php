<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMmidTeamMembers2 extends Migration
{
    public function up()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->text('divisions')->nullable();
            $table->dropColumn('division_id');
        });
    }
    
    public function down()
    {
        Schema::table('mmid_team_members', function($table)
        {
            $table->dropColumn('divisions');
            $table->integer('division_id')->nullable()->unsigned();
        });
    }
}
