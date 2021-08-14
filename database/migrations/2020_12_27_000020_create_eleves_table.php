<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElevesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'eleves';

    /**
     * Run the migrations.
     * @table eleves
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('uid', 45)->nullable()->default(null)->comment('Identifiant unique imprédictible');
            $table->integer('statut')->nullable()->default(null);
            $table->string('regnat', 45)->nullable()->default(null)->comment('numéro de registre national');
            $table->string('matricule', 45)->nullable()->default(null);
            $table->string('prenom', 45)->nullable()->default(null);
            $table->string('nom', 45)->nullable()->default(null);
            $table->date('date_nais')->nullable()->default(null);
            $table->date('date_inscript')->nullable()->default(null);
            $table->string('sexe', 2)->nullable()->default(null);
            $table->integer('solde')->nullable()->default(null);
            $table->string('siel_classe', 45)->nullable()->default(null);
            $table->unsignedBigInteger('classe_id');
            $table->string('email', 120)->nullable()->default(null)->comment('e-mail élève éventuel');
            $table->string('type_responsable_1', 45)->nullable()->default(null);
            $table->string('prenom_resp_1', 120)->nullable()->default(null);
            $table->string('nom_resp_1', 120)->nullable()->default(null);
            $table->string('iban_r1', 45)->nullable()->default(null);
            $table->string('email_r1', 120)->nullable()->default(null);
            $table->string('type_responsable_2', 45)->nullable()->default(null);
            $table->string('prenom_resp_2', 120)->nullable()->default(null);
            $table->string('nom_resp_2', 120)->nullable()->default(null);
            $table->string('iban_r2', 45)->nullable()->default(null);
            $table->string('email_r2', 120)->nullable()->default(null);
            $table->text('brol')->nullable()->default(null);

            $table->index(["classe_id"], 'fk_eleves_classes');

            $table->unique(["uid"], 'uid_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('classe_id', 'fk_eleves_classes')
                ->references('id')->on('classes')
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
