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
        Schema::create('purchased_orders', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->longText('notes')->nullable(); //need to refresh the migration
            $table->string('supplier');
            $table->longText('delivery_address');
            $table->foreignId('req_id')->constrained('requisitions', 'req_id');
            $table->json('purchased_items');
            $table->double('order_total');
            $table->string('payment');
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
        Schema::dropIfExists('purchased_orders');
    }
};
