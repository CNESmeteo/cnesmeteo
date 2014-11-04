<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCloudsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_clouds', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('measurement_id')->unsigned()->index(); // Measurement
            $table->text('value'); // JSON scheme

            $table->boolean('globe_protocol')->default(false);

            $table->text('observer_name')->nullable();

            $table->enum('direction',
                ['G', 'N', 'E', 'S', 'W']) // North, East, South, West, Global (GLOBE protocol)
            ->default('G');

            $table->enum('type',
                ['cirrocumulus', 'cirrostratus', 'cirrus',                                  // High Clouds
                 'altocumulus', 'altostratus',                                              // Medium Clouds
                 'cumulus', 'stratus', 'stratocumulus', 'nimbostratus', 'cumulonimbus'])    // Low Clouds
            ->default('Cumulus');


            // GLOBE protocol
            // --------------
            $table->enum('sky_state',
                ['clear', 'visible', 'obscured'])
            ->default('clear');

            $table->enum('clouds_cover_percent',
                ['none', 'clear', 'isolated', 'scattered', 'broken', 'overcast'])
                 // 0%, 0-10%, 10-25%, 25-50%, 50-90%, 90-100%
            ->default('none');

            $table->enum('contrails_visibility',
                ['none', 'short_lived', 'persistent_non_spreading', 'persistent_spreading'])
            ->default('none');

            $table->enum('contrails_percent',
                ['0_10', '10_25', '25_50', '50_100']) // percentage ranges
            ->default('0_10');

        });

        // Foreign keys
        Schema::table('cnesmeteo_data_temperatures', function($table)
        {
            $table->foreign('measurement_id')
                ->references('id')
                ->on('cnesmeteo_data_measurements')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_clouds');
    }

}
