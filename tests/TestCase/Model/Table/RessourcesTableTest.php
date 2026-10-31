<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RessourcesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RessourcesTable Test Case
 */
class RessourcesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RessourcesTable
     */
    protected $Ressources;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Ressources',
        'app.Etablissements',
        'app.Blocages',
        'app.Reservations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Ressources') ? [] : ['className' => RessourcesTable::class];
        $this->Ressources = $this->getTableLocator()->get('Ressources', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Ressources);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\RessourcesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\RessourcesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
