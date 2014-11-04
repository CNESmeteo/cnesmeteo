<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSkiesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_skies', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('measurement_id')->unsigned()->index(); // Measurement
            $table->integer('skytype_id')->unsigned()->index(); // Sky Type
        });

        // Foreign keys
        Schema::table('cnesmeteo_data_skies', function($table)
        {
            $table->foreign('measurement_id')
                ->references('id')
                ->on('cnesmeteo_data_measurements')
                ->onDelete('cascade');

            $table->foreign('skytype_id')
                ->references('id')
                ->on('cnesmeteo_data_skytypes');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_skies');
    }

}
