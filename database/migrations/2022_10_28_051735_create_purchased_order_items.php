<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchased_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchased_orders');
            $table->foreignId('item_id')->constrained('items', 'item_id');
            $table->foreignId('unit_id')->constrained('units', 'unit_id');
            $table->double('qty');
            $table->double('amount');
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
        Schema::dropIfExists('purchased_order_items');
    }
};
