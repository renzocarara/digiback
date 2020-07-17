<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->string('method', 20);
            $table->string('url',255);
            $table->string('domain',255);
            $table->string('scheme',20);
            $table->string('path',100,)->nullable();
            $table->string('statusline',80);
            $table->unsignedSmallInteger('status')->nullable();
            $table->string('date',50);
            $table->string('server',100);
            $table->string('location', 255)->nullable();
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
        Schema::dropIfExists('results');
    }
}
