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
        Schema::create('requisition_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('reference');
            $table->integer('type');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->longText('context');
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
        Schema::dropIfExists('requisition_notifications');
    }
};
