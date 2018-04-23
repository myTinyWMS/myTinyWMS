<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitToArticleQuantityChangelog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_quantity_changelogs', function (Blueprint $table) {
            $table->integer('unit_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_quantity_changelogs', function (Blueprint $table) {
            $table->dropColumn('unit_id');
        });
    }
}
