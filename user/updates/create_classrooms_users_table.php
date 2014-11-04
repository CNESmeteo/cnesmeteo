<?php namespace CnesMeteo\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateClassroomsUsersTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_user_classrooms_users', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('classroom_id')->unsigned()->nullable()->index();
            $table->integer('user_id')->unsigned()->nullable()->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cnesmeteo_user_classrooms_users');
    }

}
