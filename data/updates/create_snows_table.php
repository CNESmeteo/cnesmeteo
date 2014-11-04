<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSnowsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_snows', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('measurement_id')->unsigned()->index(); // Measurement
            $table->decimal('value', 9, 6)->nullable(); // Value
        });

        // Foreign keys
        Schema::table('cnesmeteo_data_snows', function($table)
        {
            $table->foreign('measurement_id')
                ->references('id')
                ->on('cnesmeteo_data_measurements')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_snows');
    }

}
