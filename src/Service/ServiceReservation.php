<?php
declare(strict_types=1);

namespace App\Service;

use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use InvalidArgumentException;

/**
 * Toute la logique de réservation : validation, verrouillage de la ressource
 * et détection des conflits (chevauchements et blocages).
 */
class ServiceReservation
{
    use LocatorAwareTrait;

    /** Statuts qui occupent un créneau. */
    public const STATUTS_ACTIFS = ['en_attente', 'confirmee'];

    public function reserver(array $donnees)
    {
        $ressourceId = (int)($donnees['ressource_id'] ?? 0);
        $debut = new DateTime($donnees['debut'] ?? 'now');
        $fin = new DateTime($donnees['fin'] ?? 'now');

        if ($ressourceId <= 0) {
            throw new InvalidArgumentException('Ressource manquante.');
        }
        if ($fin <= $debut) {
            throw new InvalidArgumentException('La fin doit être après le début.');
        }
        if ($debut < DateTime::now()) {
            throw new InvalidArgumentException('Le début doit être dans le futur.');
        }
        if (empty($donnees['nom_client']) && empty($donnees['utilisateur_id'])) {
            throw new InvalidArgumentException('Le nom du client est obligatoire.');
        }

        $reservations = $this->fetchTable('Reservations');

        return $reservations->getConnection()->transactional(
            function () use ($reservations, $ressourceId, $debut, $fin, $donnees) {
                $ressource = $this->fetchTable('Ressources')->find()
                    ->where(['id' => $ressourceId])
                    ->epilog('FOR UPDATE')
                    ->first();
                if ($ressource === null) {
                    throw new InvalidArgumentException('Ressource introuvable.');
                }

                if ($this->verifierConflit($ressourceId, $debut, $fin)) {
                    throw new ConflitReservationException(
                        "Ce créneau n'est plus disponible. Veuillez choisir un autre horaire."
                    );
                }

                $reservation = $reservations->newEntity([
                    'ressource_id' => $ressourceId,
                    'utilisateur_id' => $donnees['utilisateur_id'] ?? null,
                    'nom_client' => $donnees['nom_client'] ?? null,
                    'telephone_client' => $donnees['telephone_client'] ?? null,
                    'email_client' => $donnees['email_client'] ?? null,
                    'debut' => $debut,
                    'fin' => $fin,
                    'statut' => 'en_attente',
                    'source' => $donnees['source'] ?? 'en_ligne',
                ]);

                return $reservations->saveOrFail($reservation);
            }
        );
    }

    public function verifierConflit(
        int $ressourceId,
        DateTime $debut,
        DateTime $fin,
        ?int $ignorerReservationId = null,
    ): bool {
        $reservations = $this->fetchTable('Reservations')->find()
            ->where([
                'ressource_id' => $ressourceId,
                'statut IN' => self::STATUTS_ACTIFS,
                'debut <' => $fin,
                'fin >' => $debut,
            ]);
        if ($ignorerReservationId !== null) {
            $reservations->where(['id !=' => $ignorerReservationId]);
        }
        if ($reservations->count() > 0) {
            return true;
        }

        return $this->fetchTable('Blocages')->find()
            ->where([
                'ressource_id' => $ressourceId,
                'debut <' => $fin,
                'fin >' => $debut,
            ])
            ->count() > 0;
    }

    public function annuler(int $reservationId)
    {
        $reservations = $this->fetchTable('Reservations');
        $reservation = $reservations->get($reservationId);
        $reservation->statut = 'annulee';

        return $reservations->saveOrFail($reservation);
    }

    public function expirerEnAttente(int $minutes = 15): int
    {
        $limite = DateTime::now()->subMinutes($minutes);

        return $this->fetchTable('Reservations')->updateAll(
            ['statut' => 'expiree'],
            ['statut' => 'en_attente', 'created <' => $limite],
        );
    }
}