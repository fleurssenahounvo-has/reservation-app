<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateRessources extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('ressources');
        $table->addColumn('etablissement_id', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 150,
            'null' => false,
        ]);
        $table->addColumn('type', 'string', [
            'default' => null,
            'limit' => 30,
            'null' => false,
        ]);
        $table->addColumn('capacite', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('prix', 'decimal', [
            'default' => null,
            'null' => true,
            'precision' => 10,
            'scale' => 2,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => true,
        ]);
        $table->addForeignKey('etablissement_id', 'etablissements', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ]);
        $table->create();
    }
}