<?php namespace CnesMeteo\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateClassroomsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_user_classrooms', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('organization_id')->unsigned()->nullable()->index();

            $table->string('name');
            $table->integer('academic_year_start')->unsigned();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cnesmeteo_user_classrooms');
    }

}
