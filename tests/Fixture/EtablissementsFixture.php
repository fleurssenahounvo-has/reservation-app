<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EtablissementsFixture
 */
class EtablissementsFixture extends TestFixture
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
                'nom' => 'Lorem ipsum dolor sit amet',
                'type' => 'Lorem ipsum dolor sit amet',
                'adresse' => 'Lorem ipsum dolor sit amet',
                'telephone' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-11-01 09:35:23',
                'modified' => '2026-11-01 09:35:23',
            ],
        ];
        parent::init();
    }
}
