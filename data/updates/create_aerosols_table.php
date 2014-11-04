<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateAerosolsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_aerosols', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('measurement_id')->unsigned()->index(); // Measurement
            $table->integer('photometer_id')->unsigned()->index(); // Photometer

            $table->decimal('aot_red', 6, 4)->nullable(); // AOT Value
            $table->decimal('aot_green', 6, 4)->nullable(); // AOT Value
            $table->decimal('aot_blue', 6, 4)->nullable(); // AOT Value

            $table->boolean('globe_protocol')->default(false);

            // GLOBE protocol
            // --------------
            $table->enum('observed_sky_color',
                ['deep_blue', 'blue', 'light_blue', 'pale_blue', 'milky'])
                ->default('blue');

            $table->enum('observed_sky_clarity',
            ['unusually_clear', 'clear', 'somewhat_hazy', 'very_hazy', 'extremely_hazy'])
            ->default('clear');

            $table->decimal('voltage_temperature', 8, 3)->nullable(); // Multiply voltage x 100 to get the temperature
            $table->decimal('voltage_light', 8, 3)->nullable(); // Max voltage in the sun
            $table->decimal('voltage_dark', 8, 3)->nullable(); // Voltage in the dark

        });

        // Foreign keys
        Schema::table('cnesmeteo_data_temperatures', function($table)
        {
            $table->foreign('measurement_id')
                ->references('id')
                ->on('cnesmeteo_data_measurements')
                ->onDelete('cascade');

            $table->foreign('photometer_id')
                ->references('id')
                ->on('cnesmeteo_data_photometers');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_aerosols');
    }

}
