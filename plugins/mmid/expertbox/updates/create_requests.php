<?php namespace Mmid\Expertbox\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRequests extends Migration{

    public function up(){
        Schema::create('mmid_expertbox_requests', function($table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('is_sent')->nullable()->unsigned()->default(0);
            $table->integer('txn_status')->nullable()->unsigned()->default(0);
            $table->string('txn_id', 255)->nullable();
            $table->double('txn_amount')->nullable()->default(0);
            $table->string('txn_currency', 255)->nullable();
            $table->string('txn_gateway', 255)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('gender', 255)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->text('question')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down(){
        Schema::dropIfExists('mmid_expertbox_requests');
    }

}