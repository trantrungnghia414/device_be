<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_classrooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('equipment_id');
            $table->unsignedInteger('classroom_id');
            $table->integer('quantity');
            $table->foreign('equipment_id')->references('id')->on('equipments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_classrooms');
    }
};
