<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('internal_order_number');
            $table->string('external_order_number')->nullable();
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->integer('supplier_id')->unsigned();
            $table->integer('total_cost')->unsigned()->default(0);
            $table->integer('shipping_cost')->unsigned()->default(0);
            $table->date('expected_delivery')->nullable();
            $table->date('order_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
