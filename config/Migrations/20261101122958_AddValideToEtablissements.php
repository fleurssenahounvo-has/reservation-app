<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddValideToEtablissements extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
    $table = $this->table('etablissements');
    $table->addColumn('valide', 'boolean', ['default' => false, 'null' => false]);
    $table->update();
    }
}
