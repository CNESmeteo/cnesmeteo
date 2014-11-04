<?php namespace CnesMeteo\Data\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateMeasurementsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_data_measurements', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->enum('type',
                ['temperature', 'pressure', 'humidity', 'snow', 'rainfall', 'sky', 'clouds', 'windforce', 'winddirection', 'aerosols'])
                ->default('clouds');

            $table->integer('user_id')->unsigned()->index(); // User
            $table->integer('organization_id')->unsigned()->index(); // Organization
            $table->integer('site_id')->unsigned()->index(); // Site

            $table->string('comments', 140)->nullable(); // Twitter size comment
            $table->boolean('inGLOBE')->default(false);

            $table->timestamp('measured_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

        });

        // Foreign keys
        Schema::table('cnesmeteo_data_measurements', function($table)
        {
            $table->foreign('user_id')->references('id')->on('cnesmeteo_user_users');
            $table->foreign('organization_id')->references('id')->on('cnesmeteo_user_organizations');
            $table->foreign('site_id')->references('id')->on('cnesmeteo_user_sites');
        });
    }

    public function down()
    {
        Schema::drop('cnesmeteo_data_measurements');
    }

}
