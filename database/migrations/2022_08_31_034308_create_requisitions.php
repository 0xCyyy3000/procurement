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
            $table->string('maker');
            $table->string('priority');
            $table->longText('description');
            $table->string('status');
            $table->string('evaluator')->nullable();
            $table->json('signatories')->nullable();
            $table->integer('approval_count');
            $table->longText('message')->nullable();
            $table->foreignId('supplier')->nullable()->constrained('suppliers');
            $table->boolean('released');
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
        Schema::dropIfExists('requisitions');
    }
};
