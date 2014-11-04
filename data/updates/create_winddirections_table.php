<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateWinddirectionsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_winddirections', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('measurement_id')->unsigned()->index(); // Measurement
            $table->enum('value',
                ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'])
                ->default('N');
        });

        // Foreign keys
        Schema::table('cnesmeteo_data_winddirections', function($table)
        {
            $table->foreign('measurement_id')
                ->references('id')
                ->on('cnesmeteo_data_measurements')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_winddirections');
    }

}
