<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClasseUserTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'classe_user';

    /**
     * Run the migrations.
     * @table classe_user
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('poste', 45)->nullable()->default(null)->comment('p. ex. prof de dessin');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('user_id');

            $table->index(["user_id"], 'fk_classe_user_users_idx');

            $table->index(["classe_id"], 'fk_classe_user_classes_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('classe_id', 'fk_classe_user_classes_idx')
                ->references('id')->on('classes')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_classe_user_users_idx')
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
