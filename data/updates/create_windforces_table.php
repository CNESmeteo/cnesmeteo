<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateWindforcesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_windforces', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('measurement_id')->unsigned()->index(); // Measurement
            $table->integer('value')->unsigned(); // Value
        });

        // Foreign keys
        Schema::table('cnesmeteo_data_windforces', function($table)
        {
            $table->foreign('measurement_id')
                ->references('id')
                ->on('cnesmeteo_data_measurements')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_windforces');
    }

}
