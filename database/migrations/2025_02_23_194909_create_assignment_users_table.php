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
        Schema::create('assignment_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assignment_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('status')->default(0);
            $table->integer('completion_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
           
            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_users');
    }
};
