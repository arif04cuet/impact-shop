<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMmidTeamDivisions extends Migration
{
    public function up()
    {
        Schema::create('mmid_team_divisions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('title');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mmid_team_divisions');
    }
}
