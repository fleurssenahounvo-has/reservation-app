<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RessourcesFixture
 */
class RessourcesFixture extends TestFixture
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
                'etablissement_id' => 1,
                'nom' => 'Lorem ipsum dolor sit amet',
                'type' => 'Lorem ipsum dolor sit amet',
                'capacite' => 1,
                'prix' => 1.5,
                'created' => '2026-10-31 18:26:11',
                'modified' => '2026-10-31 18:26:11',
            ],
        ];
        parent::init();
    }
}
