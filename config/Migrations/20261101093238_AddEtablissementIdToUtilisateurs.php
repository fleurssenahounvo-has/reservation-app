<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddEtablissementIdToUtilisateurs extends BaseMigration
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
    $table = $this->table('utilisateurs');
    $table->addColumn('etablissement_id', 'integer', ['null' => true]);
    $table->addForeignKey('etablissement_id', 'etablissements', 'id', [
        'delete' => 'SET_NULL',
        'update' => 'NO_ACTION',
    ]);
    $table->update();
}
}
