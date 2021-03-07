<?php namespace Mmid\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMmidTeamMembers extends Migration
{
    public function up()
    {
        Schema::create('mmid_team_members', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('linkedin')->nullable();
            $table->integer('division_id')->nullable()->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mmid_team_members');
    }
}
