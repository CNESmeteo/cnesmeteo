<?php namespace CnesMeteo\Localization\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProvincesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_localization_provinces', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('state_id')->unsigned()->index();
            $table->string('name')->index();
            $table->string('code');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_localization_provinces');
    }

}
