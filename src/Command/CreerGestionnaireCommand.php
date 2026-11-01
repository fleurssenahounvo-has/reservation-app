<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\LocatorAwareTrait;

class CreerGestionnaireCommand extends Command
{
    use LocatorAwareTrait;

    public static function defaultName(): string
    {
        return 'creer_gestionnaire';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArgument('email', ['help' => 'Adresse e-mail', 'required' => true])
            ->addArgument('nom', ['help' => 'Nom complet', 'required' => true])
            ->addArgument('etablissement_id', ['help' => "Numéro de l'établissement (gestionnaire seulement)", 'required' => false])
            ->addOption('role', [
                'help' => 'gestionnaire ou administrateur',
                'default' => 'gestionnaire',
                'choices' => ['gestionnaire', 'administrateur'],
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $role = (string)$args->getOption('role');
        $etablissementId = $args->getArgument('etablissement_id');
        if ($role === 'gestionnaire' && empty($etablissementId)) {
            $io->err("Un gestionnaire doit avoir un numéro d'établissement.");

            return static::CODE_ERROR;
        }

        $motDePasse = (string)$io->ask('Mot de passe (8 caractères minimum)');
        if (strlen($motDePasse) < 8) {
            $io->err('Mot de passe trop court.');

            return static::CODE_ERROR;
        }

        $table = $this->fetchTable('Utilisateurs');
        $utilisateur = $table->newEntity([
            'email' => $args->getArgument('email'),
            'nom' => $args->getArgument('nom'),
            'mot_de_passe' => $motDePasse,
            'role' => $role,
            'etablissement_id' => $role === 'gestionnaire' ? (int)$etablissementId : null,
        ]);
        if (!$table->save($utilisateur)) {
            $io->err('Échec : ' . json_encode($utilisateur->getErrors()));

            return static::CODE_ERROR;
        }
        $io->out('Compte créé : ' . $utilisateur->email . ' (' . $role . ')');

        return static::CODE_SUCCESS;
    }
}