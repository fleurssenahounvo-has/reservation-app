<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\ServiceReservation;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Lance plusieurs processus qui réservent le même créneau au même instant.
 * Un seul doit réussir. Usage : bin\cake test_concurrence [--processus 6] [--manches 5]
 */
class TestConcurrenceCommand extends Command
{
    use LocatorAwareTrait;

    public static function defaultName(): string
    {
        return 'test_concurrence';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('processus', ['help' => 'Demandes simultanées par manche', 'default' => '6'])
            ->addOption('manches', ['help' => 'Nombre de manches', 'default' => '5']);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $nb = max(2, (int)$args->getOption('processus'));
        $manches = max(1, (int)$args->getOption('manches'));

        $etablissements = $this->fetchTable('Etablissements');
        $etab = $etablissements->saveOrFail($etablissements->newEntity([
            'nom' => 'TEST concurrence',
            'type' => 'salle',
        ]));
        $ressources = $this->fetchTable('Ressources');
        $ressource = $ressources->saveOrFail($ressources->newEntity([
            'etablissement_id' => $etab->id,
            'nom' => 'TEST salle concurrence',
            'type' => 'conference',
            'capacite' => 50,
        ]));

        $echecs = 0;
        try {
            $io->out("{$manches} manches de {$nb} demandes simultanées sur le même créneau.");
            for ($m = 1; $m <= $manches; $m++) {
                $jour = DateTime::now()->addDays(20 + $m);
                $debut = $jour->setTime(10, 0, 0);
                $fin = $jour->setTime(12, 0, 0);
                $barriere = microtime(true) + 2.5;

                $lances = [];
                for ($i = 0; $i < $nb; $i++) {
                    $commande = [
                        PHP_BINARY,
                        ROOT . DS . 'bin' . DS . 'cake.php',
                        'reserver_concurrent',
                        (string)$ressource->id,
                        $debut->format('Y-m-d H:i:s'),
                        $fin->format('Y-m-d H:i:s'),
                        sprintf('%.3F', $barriere),
                    ];
                    $tubes = [];
                    $processus = proc_open($commande, [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $tubes, ROOT);
                    if (!is_resource($processus)) {
                        $io->err('Impossible de lancer un processus.');

                        return static::CODE_ERROR;
                    }
                    $lances[] = [$processus, $tubes[1]];
                }

                $ok = 0;
                $conflits = 0;
                $autres = [];
                foreach ($lances as [$processus, $tube]) {
                    $sortie = (string)stream_get_contents($tube);
                    fclose($tube);
                    proc_close($processus);
                    if (str_contains($sortie, 'RESULTAT:OK')) {
                        $ok++;
                    } elseif (str_contains($sortie, 'RESULTAT:CONFLIT')) {
                        $conflits++;
                    } else {
                        $autres[] = trim(substr($sortie, 0, 300));
                    }
                }

                $actives = $this->fetchTable('Reservations')->find()
                    ->where([
                        'ressource_id' => $ressource->id,
                        'statut IN' => ServiceReservation::STATUTS_ACTIFS,
                        'debut >=' => $jour->setTime(0, 0, 0),
                        'debut <' => $jour->setTime(0, 0, 0)->addDays(1),
                    ])
                    ->count();

                $bon = $ok === 1 && $conflits === $nb - 1 && $actives === 1 && $autres === [];
                $io->out(sprintf(
                    '%s Manche %d : %d acceptée(s), %d refusée(s), %d erreur(s) ; réservations en base : %d',
                    $bon ? 'OK    ' : 'ECHEC ',
                    $m,
                    $ok,
                    $conflits,
                    count($autres),
                    $actives,
                ));
                foreach ($autres as $texte) {
                    $io->out('         ' . $texte);
                }
                if (!$bon) {
                    $echecs++;
                }
            }
        } finally {
            $etablissements->delete($etab);
            $io->out('Données de test supprimées.');
        }

        $io->out($echecs === 0
            ? 'Résultat : jamais plus d\'une réservation acceptée par créneau.'
            : "Résultat : {$echecs} manche(s) en échec.");

        return $echecs === 0 ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}