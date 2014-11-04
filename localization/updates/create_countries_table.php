<?php namespace CnesMeteo\Localization\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCountriesTable extends Migration
{

    public function up()
    {
        Schema::table('rainlab_user_countries', function($table)
        {
            $table->enum('continent', ['europe','america' ,'south_america', 'south_america', 'africa'])->default('europe');
        });
    }

    public function down()
    {
        Schema::table('rainlab_user_countries', function($table)
        {
            $table->dropColumn('continent');
        });
    }

}
