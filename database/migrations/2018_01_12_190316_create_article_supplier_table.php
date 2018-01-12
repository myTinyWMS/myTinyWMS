<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_supplier', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('article_id')->unsigned();
            $table->integer('supplier_id')->unsigned();

            $table->string('order_number');
            $table->double('price');
            $table->integer('delivery_time');
            $table->integer('order_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_supplier');
    }
}
