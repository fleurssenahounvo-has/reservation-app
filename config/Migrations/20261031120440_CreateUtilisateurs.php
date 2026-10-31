<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUtilisateurs extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('utilisateurs');
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('email', 'string', [
            'default' => null,
            'limit' => 150,
            'null' => false,
        ]);
        $table->addColumn('mot_de_passe', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('role', 'string', [
            'default' => 'client',
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => true,
        ]);
        $table->addIndex(['email'], ['unique' => true]);
        $table->create();
    }
}