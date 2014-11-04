<?php namespace CnesMeteo\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateClassroomsSitesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_user_classrooms_sites', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('classroom_id')->unsigned()->nullable()->index();
            $table->integer('site_id')->unsigned()->nullable()->index();
        });
    }

    public function down()
    {
        //Schema::drop('cnesmeteo_user_classrooms_sites');
    }

}
