<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryItemIdToArticleQuantityChangelog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_quantity_changelogs', function (Blueprint $table) {
            $table->integer('delivery_item_id')->unsigned()->nullable();
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
            $table->dropColumn('delivery_item_id');
        });
    }
}
