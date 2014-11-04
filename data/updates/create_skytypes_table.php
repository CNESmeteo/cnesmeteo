<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSkytypesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_skytypes', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name'); // English
            $table->enum('type',
                ['Sunny', 'Cloudy', 'Rainy'])
                ->default('Sunny');

            // image <--> attachOne
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_skytypes');
    }

}
