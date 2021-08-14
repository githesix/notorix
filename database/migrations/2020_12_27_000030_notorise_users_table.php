<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotoriseUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('uid', 45)->nullable()->default(null)->comment('Unpredictable unique identifier');
            $table->integer('statut')->nullable()->default(null);
            $table->string('username', 191)->comment('same as email');
            $table->string('secu', 252)->nullable()->default(null);
            $table->string('sexe', 8)->nullable()->default(null)->comment('H/F');
            $table->string('prenom', 100)->nullable()->default(null)->comment('First name');
            $table->string('nom', 100)->comment('Family name required');
            $table->string('tel1', 16)->comment('Main phone number');
            $table->string('tel2', 16)->comment('Backup phone number');
            $table->string('ou', 45)->nullable()->default(null)->comment('Organizational Unit (for inst. ARCProf)');
            $table->text('memo')->nullable()->default(null)->comment('Coord, tel...');
            $table->integer('role')->nullable()->default(null);
            $table->integer('solde')->nullable()->default(null);
            $table->integer('limite')->nullable()->default(null);
            $table->string('iban', 45)->nullable()->default(null)->comment('European bank account number');
            $table->unsignedBigInteger('elu')->nullable()->default(null)->comment('eleve_id if user is a student');

            $table->index(["elu"], 'fk_elu_idx');

            $table->unique(["uid"], 'uid_idx');

            $table->foreign('elu', 'fk_elu_idx')
                ->references('id')->on('eleves')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_elu_idx');
            $table->dropIndex('uid_idx', 'fk_elu_idx');
            $table->dropColumn('uid', 'username', 'statut', 'secu', 'sexe', 'prenom', 'tel1', 'tel2', 'nom', 'ou', 'memo', 'role', 'solde','limite', 'iban', 'elu');
        });
    }
}
