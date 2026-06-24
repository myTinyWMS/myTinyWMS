<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExportPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->index(['auditable_type', 'auditable_id', 'created_at'], 'audits_auditable_created_at_index');
        });

        Schema::table('article_quantity_changelogs', function (Blueprint $table) {
            $table->index(['article_id', 'created_at', 'type'], 'aqc_article_created_type_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex('audits_auditable_created_at_index');
        });

        Schema::table('article_quantity_changelogs', function (Blueprint $table) {
            $table->dropIndex('aqc_article_created_type_index');
        });
    }
}
