<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\ConflitReservationException;
use App\Service\ServiceReservation;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Vérifie la détection des conflits : bin\cake test_reservation
 */
class TestReservationCommand extends Command
{
    use LocatorAwareTrait;

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $service = new ServiceReservation();
        $etablissements = $this->fetchTable('Etablissements');

        $etab = $etablissements->saveOrFail($etablissements->newEntity([
            'nom' => 'TEST établissement',
            'type' => 'salle',
        ]));
        $ressource = $this->fetchTable('Ressources')->saveOrFail(
            $this->fetchTable('Ressources')->newEntity([
                'etablissement_id' => $etab->id,
                'nom' => 'TEST salle',
                'type' => 'conference',
                'capacite' => 50,
            ])
        );

        $jour = DateTime::now()->addDays(10);
        $h = fn (int $heure): string => $jour->setTime($heure, 0, 0)->format('Y-m-d H:i:s');

        $essayer = function (string $titre, int $debut, int $fin, bool $attenduOk) use ($service, $ressource, $h, $io) {
            try {
                $r = $service->reserver([
                    'ressource_id' => $ressource->id,
                    'nom_client' => 'Client test',
                    'debut' => $h($debut),
                    'fin' => $h($fin),
                ]);
                $ok = true;
            } catch (ConflitReservationException $e) {
                $r = null;
                $ok = false;
            }
            $verdict = $ok === $attenduOk ? 'OK    ' : 'ECHEC ';
            $io->out(sprintf(
                '%s %s : %s (attendu : %s)',
                $verdict,
                $titre,
                $ok ? 'accepté' : 'refusé',
                $attenduOk ? 'accepté' : 'refusé'
            ));

            return $r;
        };

        $io->out('Jour du test : ' . $jour->format('d/m/Y'));
        $premiere = $essayer('14h-16h (créneau libre)', 14, 16, true);
        $essayer('15h-17h (chevauche 14h-16h)', 15, 17, false);
        $essayer('14h-16h (même créneau)', 14, 16, false);
        $essayer('16h-18h (juste après, sans chevauchement)', 16, 18, true);

        $blocages = $this->fetchTable('Blocages');
        $blocages->saveOrFail($blocages->newEntity([
            'ressource_id' => $ressource->id,
            'debut' => $h(18),
            'fin' => $h(20),
            'motif' => 'TEST fermeture',
        ]));
        $essayer('19h-20h (période bloquée)', 19, 20, false);

        $service->annuler((int)$premiere->id);
        $essayer('14h-16h après annulation', 14, 16, true);

        // Nettoyage : supprime l'établissement (les ressources et réservations suivent par CASCADE).
        $etablissements->delete($etab);
        $io->out('Données de test supprimées.');

        return static::CODE_SUCCESS;
    }
}