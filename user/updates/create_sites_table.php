<?php namespace CnesMeteo\User\Updates;

use Schema;
use Config;
use October\Rain\Database\Updates\Migration;

class CreateSitesTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_user_sites', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('organization_id')->unsigned()->nullable()->index();
            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->integer('state_id')->unsigned()->nullable()->index();
            $table->integer('province_id')->unsigned()->nullable()->index();

            $table->string('name');
            $table->string('address');
            $table->text('location')->nullable(); // Format: "State, Province (Country)"

            $table->decimal('altitude', 9, 2)->nullable(); // meters
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('timezone')->default( Config::get('app.timezone', 'UTC') );

            $table->text('settings')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cnesmeteo_user_sites');
    }

}
