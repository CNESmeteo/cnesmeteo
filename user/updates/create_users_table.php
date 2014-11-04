<?php namespace CnesMeteo\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::table('backend_users', function($table)
        {
            // CnesMeteo.Localization
            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->integer('state_id')->unsigned()->nullable()->index();
            $table->integer('province_id')->unsigned()->nullable()->index();
            $table->integer('language_id')->unsigned()->nullable()->index();

            // User Profile
            $table->text('address')->nullable();
            $table->string('phone', 16)->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('backend_users', 'country_id'))
        {
            Schema::table('backend_users', function($table)
            {
                $table->dropColumn([
                    'country_id', 'state_id', 'province_id', 'language_id',
                    'address', 'phone'
                ]);
            });
        }
    }

}
