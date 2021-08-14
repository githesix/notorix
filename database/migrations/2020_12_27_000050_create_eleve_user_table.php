<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEleveUserTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'eleve_user';

    /**
     * Run the migrations.
     * @table eleve_user
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('lien', 45)->nullable()->default(null)->comment('resp_1, resp_2, resp_x');
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('user_id');

            $table->index(["eleve_id"], 'fk_eleve_user_eleves_idx');

            $table->index(["user_id"], 'fk_eleve_user_users_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('eleve_id', 'fk_eleve_user_eleves_idx')
                ->references('id')->on('eleves')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_eleve_user_users_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
