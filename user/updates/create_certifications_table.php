<?php namespace CnesMeteo\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCertificationsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_user_certifications', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->index(); // User
            $table->integer('checker_id')->unsigned()->index(); // User

            $table->string('globeID');
            $table->boolean('certified')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        //Schema::drop('cnesmeteo_user_certifications');
    }

}
