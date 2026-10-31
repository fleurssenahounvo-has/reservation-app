<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EtablissementsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EtablissementsTable Test Case
 */
class EtablissementsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EtablissementsTable
     */
    protected $Etablissements;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Etablissements',
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
        $config = $this->getTableLocator()->exists('Etablissements') ? [] : ['className' => EtablissementsTable::class];
        $this->Etablissements = $this->getTableLocator()->get('Etablissements', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Etablissements);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\EtablissementsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
