<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreDetailsToOrderItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->date('expected_delivery')->nullable();
            $table->boolean('confirmation_received')->default(false);
            $table->boolean('invoice_received')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('expected_delivery');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('confirmation_received');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('invoice_received');
        });
    }
}
