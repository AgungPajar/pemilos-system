<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePklStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkl_students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('nip')->unique();
            $table->string('jk', 10)->nullable();
            $table->string('nisn')->nullable();
            $table->string('tmp_lahir')->nullable();
            $table->date('tgl_lahir');
            $table->string('kelas');
            $table->foreignId('token_id')->nullable()->unique()->constrained('tokens')->nullOnDelete();
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
        Schema::dropIfExists('pkl_students');
    }
}
