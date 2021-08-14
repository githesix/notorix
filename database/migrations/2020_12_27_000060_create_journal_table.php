<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'journal';

    /**
     * Run the migrations.
     * @table journal
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('categorie', 45)->nullable()->default(null)->comment('auth, financier, message, population...');
            $table->integer('public')->nullable()->default(null)->comment('bitwise: tous, eleves, profs, admin...');
            $table->string('action', 160)->nullable()->default(null)->comment('Description');
            $table->text('memo')->nullable()->default(null)->comment('Réservé à un usage futur');
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->comment('Facultatif (cf. cases Mandatory de l\'onglet Foreign Key dans la relation)');

            $table->index(["user_id"], 'fk_journal_users_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_journal_users_idx')
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
