<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisiSkillSubskillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divisi_skill_subskills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->foreignId('sub_skill_id')->constrained('sub_skills')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
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
        Schema::dropIfExists('divisi_skill_subskills');
    }
}
