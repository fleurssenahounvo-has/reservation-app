<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\ConflitReservationException;
use App\Service\ServiceReservation;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

class ServiceReservationTest extends TestCase
{
    use LocatorAwareTrait;

    private ServiceReservation $service;
    private $etablissement;
    private $ressource;
    private DateTime $jour;

    protected function setUp(): void
    {
        parent::setUp();

        // Sécurité : ne jamais exécuter ces tests sur la vraie base.
        $base = ConnectionManager::get('default')->config()['database'] ?? '';
        if ($base !== 'reservation_test') {
            $this->markTestSkipped("Ces tests doivent s'exécuter sur la base reservation_test (base actuelle : {$base}).");
        }

        $this->service = new ServiceReservation();
        $this->jour = DateTime::now()->addDays(10);

        $etablissements = $this->fetchTable('Etablissements');
        $this->etablissement = $etablissements->saveOrFail(
            $etablissements->newEntity(['nom' => 'TEST établissement', 'type' => 'salle'])
        );
        $ressources = $this->fetchTable('Ressources');
        $this->ressource = $ressources->saveOrFail($ressources->newEntity([
            'etablissement_id' => $this->etablissement->id,
            'nom' => 'TEST salle',
            'type' => 'conference',
            'capacite' => 50,
        ]));
    }

    protected function tearDown(): void
    {
        if (isset($this->etablissement)) {
            // Les ressources, réservations et blocages suivent par CASCADE.
            $this->fetchTable('Etablissements')->delete($this->etablissement);
        }
        parent::tearDown();
    }

    private function h(int $heure): DateTime
    {
        return $this->jour->setTime($heure, 0, 0);
    }

    private function reserver(int $debut, int $fin, string $statut = 'en_attente')
    {
        return $this->service->reserver([
            'ressource_id' => $this->ressource->id,
            'nom_client' => 'Client test',
            'debut' => $this->h($debut)->format('Y-m-d H:i:s'),
            'fin' => $this->h($fin)->format('Y-m-d H:i:s'),
        ], $statut);
    }

    public function testCreneauLibreEstAccepte(): void
    {
        $reservation = $this->reserver(14, 16);
        $this->assertSame('en_attente', $reservation->statut);
        $this->assertSame('en_ligne', $reservation->source);
    }

    public function testChevauchementPartielEstRefuse(): void
    {
        $this->reserver(14, 16);
        $this->expectException(ConflitReservationException::class);
        $this->reserver(15, 17);
    }

    public function testChevauchementQuiContientLeCreneauEstRefuse(): void
    {
        $this->reserver(14, 16);
        $this->expectException(ConflitReservationException::class);
        $this->reserver(13, 18);
    }

    public function testMemeCreneauEstRefuse(): void
    {
        $this->reserver(14, 16);
        $this->expectException(ConflitReservationException::class);
        $this->reserver(14, 16);
    }

    public function testCreneauAdjacentEstAccepte(): void
    {
        $this->reserver(14, 16);
        $apres = $this->reserver(16, 18);
        $avant = $this->reserver(12, 14);
        $this->assertNotNull($apres->id);
        $this->assertNotNull($avant->id);
    }

    public function testBlocageRefuseUneReservation(): void
    {
        $this->service->bloquerDates($this->ressource->id, $this->h(18), $this->h(20), 'TEST fermeture');
        $this->expectException(ConflitReservationException::class);
        $this->reserver(19, 20);
    }

    public function testAnnulationLibereLeCreneau(): void
    {
        $premiere = $this->reserver(14, 16);
        $this->service->annuler((int)$premiere->id);
        $nouvelle = $this->reserver(14, 16);
        $this->assertNotNull($nouvelle->id);
    }

    public function testReservationExpireeLibereLeCreneau(): void
    {
        $premiere = $this->reserver(14, 16);
        $reservations = $this->fetchTable('Reservations');
        $reservations->updateAll(
            ['created' => DateTime::now()->subMinutes(20)],
            ['id' => $premiere->id],
        );

        // reserver() lance l'expiration automatique avant de vérifier les conflits.
        $this->service->expirerEnAttente();
        $this->assertSame('expiree', $reservations->get($premiere->id)->statut);
        $this->assertNotNull($this->reserver(14, 16)->id);
    }

    public function testReservationConfirmeeNExpirePas(): void
    {
        $confirmee = $this->reserver(14, 16, 'confirmee');
        $reservations = $this->fetchTable('Reservations');
        $reservations->updateAll(
            ['created' => DateTime::now()->subMinutes(60)],
            ['id' => $confirmee->id],
        );
        $this->service->expirerEnAttente();
        $this->assertSame('confirmee', $reservations->get($confirmee->id)->statut);
    }

    public function testFinAvantDebutEstRefusee(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reserver(18, 17);
    }

    public function testDebutDansLePasseEstRefuse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->reserver([
            'ressource_id' => $this->ressource->id,
            'nom_client' => 'Client test',
            'debut' => DateTime::now()->subDays(1)->format('Y-m-d H:i:s'),
            'fin' => DateTime::now()->subDays(1)->addHours(2)->format('Y-m-d H:i:s'),
        ]);
    }

    public function testNomClientObligatoire(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->reserver([
            'ressource_id' => $this->ressource->id,
            'debut' => $this->h(14)->format('Y-m-d H:i:s'),
            'fin' => $this->h(16)->format('Y-m-d H:i:s'),
        ]);
    }

    public function testRessourceInexistanteEstRefusee(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->reserver([
            'ressource_id' => 999999,
            'nom_client' => 'Client test',
            'debut' => $this->h(14)->format('Y-m-d H:i:s'),
            'fin' => $this->h(16)->format('Y-m-d H:i:s'),
        ]);
    }

    public function testBloquerRefuseSiUneReservationExiste(): void
    {
        $this->reserver(10, 12);
        $this->expectException(ConflitReservationException::class);
        $this->service->bloquerDates($this->ressource->id, $this->h(9), $this->h(11));
    }

    public function testConfirmerNeFonctionneQueSurUneReservationEnAttente(): void
    {
        $reservation = $this->reserver(14, 16);
        $confirmee = $this->service->confirmer((int)$reservation->id);
        $this->assertSame('confirmee', $confirmee->statut);

        $this->expectException(InvalidArgumentException::class);
        $this->service->confirmer((int)$reservation->id);
    }

    public function testCalendrierDuMois(): void
    {
        $numero = (int)$this->jour->format('j');
        $annee = (int)$this->jour->format('Y');
        $mois = (int)$this->jour->format('n');

        $jours = $this->service->disponibilitesMois($this->ressource->id, $annee, $mois);
        $this->assertSame('libre', $jours[$numero]);

        $this->reserver(14, 16);
        $jours = $this->service->disponibilitesMois($this->ressource->id, $annee, $mois);
        $this->assertSame('partiel', $jours[$numero]);

        $this->service->bloquerDates(
            $this->ressource->id,
            $this->jour->setTime(0, 0, 0)->addDays(1),
            $this->jour->setTime(0, 0, 0)->addDays(2),
        );
        $lendemain = $this->jour->addDays(1);
        if ((int)$lendemain->format('n') === $mois) {
            $jours = $this->service->disponibilitesMois($this->ressource->id, $annee, $mois);
            $this->assertSame('ferme', $jours[(int)$lendemain->format('j')]);
        }
    }
}