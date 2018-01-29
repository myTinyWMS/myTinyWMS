<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleQuantityChangelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_quantity_changelogs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('article_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->tinyInteger('type')->unsigned();
            $table->integer('change');
            $table->integer('new_quantity');
            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_quantity_changelogs');
    }
}
