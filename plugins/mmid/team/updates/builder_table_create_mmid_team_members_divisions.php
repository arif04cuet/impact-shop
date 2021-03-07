<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMmidTeamMembersDivisions extends Migration
{
    public function up()
    {
        Schema::create('mmid_team_members_divisions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('member_id')->unsigned();
            $table->integer('division_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mmid_team_members_divisions');
    }
}
