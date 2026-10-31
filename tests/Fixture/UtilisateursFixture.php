<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UtilisateursFixture
 */
class UtilisateursFixture extends TestFixture
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
                'email' => 'Lorem ipsum dolor sit amet',
                'mot_de_passe' => 'Lorem ipsum dolor sit amet',
                'role' => 'Lorem ipsum dolor ',
                'created' => '2026-10-31 18:26:31',
                'modified' => '2026-10-31 18:26:31',
            ],
        ];
        parent::init();
    }
}
