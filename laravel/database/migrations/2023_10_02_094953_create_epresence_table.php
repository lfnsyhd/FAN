<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpresenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epresence', function (Blueprint $table) {
            $table->id();
            $table->integer('id_users');
            $table->enum('type', ['IN', 'OUT']);
            $table->boolean('is_approve');
            $table->dateTime('waktu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('epresence');
    }
}
