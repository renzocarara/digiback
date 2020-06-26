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
            $table->string('url',200);
            $table->string('domain',200);
            $table->string('scheme',20);
            $table->string('path',100,);
            $table->string('version',20)->nullable();
            $table->string('status',20);
            $table->string('date',50);
            $table->string('server',50);
            $table->string('location', 200)->nullable();
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
