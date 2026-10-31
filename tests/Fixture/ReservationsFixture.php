<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ReservationsFixture
 */
class ReservationsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'ressource_id' => 1,
                'utilisateur_id' => 1,
                'nom_client' => 'Lorem ipsum dolor sit amet',
                'telephone_client' => 'Lorem ipsum dolor sit amet',
                'email_client' => 'Lorem ipsum dolor sit amet',
                'debut' => '2026-10-31 18:26:44',
                'fin' => '2026-10-31 18:26:44',
                'statut' => 'Lorem ipsum dolor ',
                'source' => 'Lorem ipsum dolor ',
                'created' => '2026-10-31 18:26:44',
                'modified' => '2026-10-31 18:26:44',
            ],
        ];
        parent::init();
    }
}
