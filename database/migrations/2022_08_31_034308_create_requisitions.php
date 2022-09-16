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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id('req_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('priority');
            $table->longText('description');
            $table->string('status');
            $table->string('evaluator')->nullable();
            $table->timestamps();
        });
        //priority, description,req_date, req_time, req_status
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions');
    }
};
