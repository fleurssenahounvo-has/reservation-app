<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\ConflitReservationException;
use App\Service\ServiceReservation;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Un « client » du test de concurrence : attend un instant précis, puis tente de réserver.
 * Utilisée par test_concurrence, pas à lancer à la main.
 */
class ReserverConcurrentCommand extends Command
{
    public static function defaultName(): string
    {
        return 'reserver_concurrent';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArgument('ressource_id', ['required' => true])
            ->addArgument('debut', ['required' => true])
            ->addArgument('fin', ['required' => true])
            ->addArgument('barriere', ['required' => true]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $barriere = (float)$args->getArgument('barriere');
        while (microtime(true) < $barriere) {
            usleep(200);
        }

        try {
            (new ServiceReservation())->reserver([
                'ressource_id' => (int)$args->getArgument('ressource_id'),
                'nom_client' => 'Concurrent ' . getmypid(),
                'debut' => $args->getArgument('debut'),
                'fin' => $args->getArgument('fin'),
            ]);
            $io->out('RESULTAT:OK');
        } catch (ConflitReservationException) {
            $io->out('RESULTAT:CONFLIT');
        } catch (\Throwable $e) {
            $io->out('RESULTAT:ERREUR ' . $e->getMessage());
        }

        return static::CODE_SUCCESS;
    }
}