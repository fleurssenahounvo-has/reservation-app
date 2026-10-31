<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateBlocages extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('blocages');
        $table->addColumn('ressource_id', 'integer', [
            'null' => false,
        ]);
        $table->addColumn('debut', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('fin', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('motif', 'string', [
            'limit' => 255,
            'null' => true,
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
        $table->create();
    }
}