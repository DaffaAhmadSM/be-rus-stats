<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentals', function (Blueprint $table) {
            $table->id();
            $table->integer('communication');
            $table->integer('understanding_problem');
            $table->integer('problem_solving');
            $table->integer('creativy');
            $table->integer('team_work');
            $table->integer('discipline');
            $table->integer('adaptation');
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
        Schema::dropIfExists('mentals');
    }
}
