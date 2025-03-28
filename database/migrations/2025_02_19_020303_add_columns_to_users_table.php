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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('role_id')->after('id');
                $table->unsignedInteger('repair_team_id')->after('role_id');
                $table->string('username')->after('repair_team_id');
                $table->string('address')->nullable()->after('phone'); 
                $table->string('gender')->nullable()->after('address'); 
                $table->string('avatar')->nullable()->after('gender'); 
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('repair_team_id')->references('id')->on('repair_teams')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
