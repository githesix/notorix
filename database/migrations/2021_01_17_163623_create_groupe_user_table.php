<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupe_user', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id'); // ATTENTION vieille table: unsignedInteger / unsignedBigInteger
            $table->foreign('user_id')
            ->references('id')
            ->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('groupe_id');
            $table->foreign('groupe_id')
            ->references('id')
            ->on('groupes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groupes_users');
    }
}
