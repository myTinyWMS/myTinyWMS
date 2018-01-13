<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->integer('unit_id')->unsigned()->nullable();
            $table->integer('status')->default(0);
            $table->integer('quantity');
            $table->integer('min_quantity')->default(0);
            $table->integer('usage_quantity')->default(1);
            $table->integer('issue_quantity')->default(1);
            $table->integer('sort_id')->default(0);
            $table->boolean('inventory')->default(true);
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('articles');
    }
}
