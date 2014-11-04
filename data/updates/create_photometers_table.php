<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePhotometersTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_photometers', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name'); // English
            $table->text('description')->nullable(); // English
            $table->boolean('aot_directly')->default(true); // English
            $table->integer('wavelength_red')->unsigned()->nullable();
            $table->integer('wavelength_green')->unsigned()->nullable();
            $table->integer('wavelength_blue')->unsigned()->nullable();

            // image <--> attachOne
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_photometers');
    }

}
