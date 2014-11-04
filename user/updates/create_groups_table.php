<?php namespace CnesMeteo\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateGroupsTable extends Migration
{

    public function up()
    {
        Schema::table('backend_user_groups', function($table)
        {
            $table->text('description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('backend_user_groups', function($table)
        {
            if (Schema::hasColumn('backend_user_groups', 'description'))
            {
                $table->dropColumn('description');
            }
        });
    }

}
