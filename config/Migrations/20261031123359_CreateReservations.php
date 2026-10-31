<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateReservations extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('reservations');
        $table->addColumn('ressource_id', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('utilisateur_id', 'integer', [
            'null' => true,
        ]);
        $table->addColumn('nom_client', 'string', [
            'limit' => 100,
            'null' => true,
        ]);
        $table->addColumn('telephone_client', 'string', [
            'limit' => 30,
            'null' => true,
        ]);
        $table->addColumn('email_client', 'string', [
            'limit' => 150,
            'null' => true,
        ]);
        $table->addColumn('debut', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('fin', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('statut', 'string', [
            'default' => 'en_attente',
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('source', 'string', [
            'default' => 'en_ligne',
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => true,
        ]);
        $table->addIndex(['ressource_id', 'debut', 'fin']);
        $table->addForeignKey('ressource_id', 'ressources', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ]);
        $table->addForeignKey('utilisateur_id', 'utilisateurs', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION',
        ]);
        $table->create();
    }
}