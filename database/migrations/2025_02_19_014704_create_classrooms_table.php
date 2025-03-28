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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('building_id');
            $table->unsignedInteger('room_type_id');
            $table->string('classroom_name');
            $table->foreign('building_id')->references('id')->on('buildings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
