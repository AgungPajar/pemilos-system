<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaslonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paslons', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('order_number')->unique();
            $table->string('leader_name')->nullable();
            $table->string('deputy_name')->nullable();
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->string('tagline')->nullable();
            $table->text('vision');
            $table->text('mission');
            $table->text('program')->nullable();
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
        Schema::dropIfExists('paslons');
    }
}
