<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleGroupItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_group_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('article_group_id')->unsigned()->nullable();
            $table->integer('article_id')->unsigned();
            $table->integer('quantity')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_group_items');
    }
}
