<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BlocagesFixture
 */
class BlocagesFixture extends TestFixture
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
                'debut' => '2026-10-31 18:26:54',
                'fin' => '2026-10-31 18:26:54',
                'motif' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-10-31 18:26:54',
                'modified' => '2026-10-31 18:26:54',
            ],
        ];
        parent::init();
    }
}
