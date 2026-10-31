<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BlocagesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BlocagesTable Test Case
 */
class BlocagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BlocagesTable
     */
    protected $Blocages;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Blocages',
        'app.Ressources',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Blocages') ? [] : ['className' => BlocagesTable::class];
        $this->Blocages = $this->getTableLocator()->get('Blocages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Blocages);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\BlocagesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\BlocagesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
