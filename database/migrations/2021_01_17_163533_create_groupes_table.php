<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->nullableTimestamps();
            $table->string('nom', 45)->nullable()->default(null)->comment('nom court du groupe');
            $table->string('description', 160)->nullable()->default(null)->comment('description du groupe');
            $table->integer('statut')->nullable()->default(null);
            $table->text('brol')->nullable()->default(null);

            $table->unique(['nom']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groupes');
    }
}
