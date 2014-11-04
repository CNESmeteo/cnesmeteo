<?php namespace CnesMeteo\Localization\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateLanguagesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_localization_languages', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('enabled')->default(false);
            $table->string('name')->index();
            $table->string('code', 3);
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_localization_languages');
    }

}
