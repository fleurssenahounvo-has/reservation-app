<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UtilisateursTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UtilisateursTable Test Case
 */
class UtilisateursTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UtilisateursTable
     */
    protected $Utilisateurs;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Utilisateurs',
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
        $config = $this->getTableLocator()->exists('Utilisateurs') ? [] : ['className' => UtilisateursTable::class];
        $this->Utilisateurs = $this->getTableLocator()->get('Utilisateurs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Utilisateurs);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\UtilisateursTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\UtilisateursTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
