<?php namespace CnesMeteo\User\Updates;

use Schema;
use Config;
use October\Rain\Database\Updates\Migration;

class CreateOrganizationsTable extends Migration
{

    public function up()
    {
        Schema::create('cnesmeteo_user_organizations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->integer('state_id')->unsigned()->nullable()->index();
            $table->integer('province_id')->unsigned()->nullable()->index();
            $table->integer('language_id')->unsigned()->nullable()->index();

            $table->boolean('is_activated')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->string('activation_code')->nullable();

            // Organization Type:
            // ------------------
            // primary <--> école
            // secondary <--> collégé
            // high school <--> lycée
            // university <--> université
            $table->enum('type', ['primary', 'secondary', 'high_school', 'university'])->default('primary');

            $table->string('RNE', 8)->nullable(); // French School ID system
            $table->string('name');
            $table->text('address');
            $table->text('location')->nullable(); // Format: "State, Province (Country)"
            $table->string('phone', 16);
            $table->string('email');
            $table->string('website');

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
        Schema::dropIfExists('cnesmeteo_user_organizations');
    }

}
