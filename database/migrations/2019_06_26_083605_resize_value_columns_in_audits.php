<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResizeValueColumnsInAudits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('DB_CONNECTION') !== 'sqlite_testing') {
            DB::statement("ALTER TABLE audits MODIFY old_values mediumtext;");
            DB::statement("ALTER TABLE audits MODIFY new_values mediumtext;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
