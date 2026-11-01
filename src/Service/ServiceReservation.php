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
        /** Plage horaire affichée dans le calendrier. */
    public const HEURE_OUVERTURE = 8;
    public const HEURE_FERMETURE = 18;

        public function reserver(array $donnees, string $statut = 'en_attente', bool $verifierFutur = true)
    {
        if (!in_array($statut, ['en_attente', 'confirmee'], true)) {
            throw new InvalidArgumentException('Statut invalide.');
        }
        $this->expirerEnAttente();
        $ressourceId = (int)($donnees['ressource_id'] ?? 0);
        $debut = new DateTime($donnees['debut'] ?? 'now');
        $fin = new DateTime($donnees['fin'] ?? 'now');

        if ($ressourceId <= 0) {
            throw new InvalidArgumentException('Ressource manquante.');
        }
        if ($fin <= $debut) {
            throw new InvalidArgumentException('La fin doit être après le début.');
        }
        if ($verifierFutur && $debut < DateTime::now()) {
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

        public function confirmer(int $reservationId)
    {
        $reservations = $this->fetchTable('Reservations');
        $reservation = $reservations->get($reservationId);
        if ($reservation->statut !== 'en_attente') {
            throw new InvalidArgumentException('Seule une réservation en attente peut être confirmée.');
        }
        $reservation->statut = 'confirmee';

        return $reservations->saveOrFail($reservation);
    }

    public function annuler(int $reservationId)
    {
        $reservations = $this->fetchTable('Reservations');
        $reservation = $reservations->get($reservationId);
        if (!in_array($reservation->statut, self::STATUTS_ACTIFS, true)) {
            throw new InvalidArgumentException('Cette réservation est déjà annulée ou expirée.');
        }
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
        /**
     * Statut de chaque jour du mois : libre, partiel, complet, ferme ou passe.
     *
     * @return array<int, string> Jour du mois => statut.
     */
    public function disponibilitesMois(int $ressourceId, int $annee, int $mois): array
    {
        $premier = new DateTime(sprintf('%04d-%02d-01 00:00:00', $annee, $mois));
        $suivant = $premier->addMonths(1);

        $reservations = $this->fetchTable('Reservations')->find()
            ->where([
                'ressource_id' => $ressourceId,
                'statut IN' => self::STATUTS_ACTIFS,
                'debut <' => $suivant,
                'fin >' => $premier,
            ])->all()->toList();
        $blocages = $this->fetchTable('Blocages')->find()
            ->where([
                'ressource_id' => $ressourceId,
                'debut <' => $suivant,
                'fin >' => $premier,
            ])->all()->toList();

        $jours = [];
        $nbJours = (int)$premier->format('t');
        for ($j = 1; $j <= $nbJours; $j++) {
            $debutJour = $premier->setDate($annee, $mois, $j)->setTime(self::HEURE_OUVERTURE, 0, 0);
            $finJour = $debutJour->setTime(self::HEURE_FERMETURE, 0, 0);
            $total = (self::HEURE_FERMETURE - self::HEURE_OUVERTURE) * 60;

            $minBlocage = $this->minutesOccupees($blocages, $debutJour, $finJour);
            $minTotal = $this->minutesOccupees(array_merge($blocages, $reservations), $debutJour, $finJour);

            $statut = 'libre';
            if ($minBlocage >= $total) {
                $statut = 'ferme';
            } elseif ($minTotal >= $total) {
                $statut = 'complet';
            } elseif ($minTotal > 0) {
                $statut = 'partiel';
            }
            if ($finJour < DateTime::now()) {
                $statut = 'passe';
            }
            $jours[$j] = $statut;
        }

        return $jours;
    }

    /**
     * Créneaux de 2 heures d'un jour, avec leur disponibilité.
     *
     * @return array<int, array{debut: \Cake\I18n\DateTime, fin: \Cake\I18n\DateTime, libre: bool}>
     */
    public function creneauxJour(int $ressourceId, DateTime $jour): array
    {
        $creneaux = [];
        for ($h = self::HEURE_OUVERTURE; $h < self::HEURE_FERMETURE; $h += 2) {
            $debut = $jour->setTime($h, 0, 0);
            $fin = $jour->setTime($h + 2, 0, 0);
            $creneaux[] = [
                'debut' => $debut,
                'fin' => $fin,
                'libre' => $debut >= DateTime::now() && !$this->verifierConflit($ressourceId, $debut, $fin),
            ];
        }

        return $creneaux;
    }

    /**
     * Minutes occupées dans [debut, fin[ en fusionnant les périodes qui se chevauchent.
     */
    private function minutesOccupees(array $periodes, DateTime $debut, DateTime $fin): int
    {
        $intervalles = [];
        foreach ($periodes as $p) {
            $d = max($p->debut->getTimestamp(), $debut->getTimestamp());
            $f = min($p->fin->getTimestamp(), $fin->getTimestamp());
            if ($f > $d) {
                $intervalles[] = [$d, $f];
            }
        }
        usort($intervalles, fn ($a, $b) => $a[0] <=> $b[0]);

        $total = 0;
        $courant = null;
        foreach ($intervalles as [$d, $f]) {
            if ($courant === null) {
                $courant = [$d, $f];
            } elseif ($d <= $courant[1]) {
                $courant[1] = max($courant[1], $f);
            } else {
                $total += $courant[1] - $courant[0];
                $courant = [$d, $f];
            }
        }
        if ($courant !== null) {
            $total += $courant[1] - $courant[0];
        }

        return intdiv($total, 60);
    }
        /**
     * Bloque une période. Refuse si elle chevauche une réservation active ou un blocage.
     */
    public function bloquerDates(int $ressourceId, DateTime $debut, DateTime $fin, ?string $motif = null)
    {
        if ($fin <= $debut) {
            throw new InvalidArgumentException('La fin doit être après le début.');
        }
        $blocages = $this->fetchTable('Blocages');

        return $blocages->getConnection()->transactional(
            function () use ($blocages, $ressourceId, $debut, $fin, $motif) {
                $ressource = $this->fetchTable('Ressources')->find()
                    ->where(['id' => $ressourceId])
                    ->epilog('FOR UPDATE')
                    ->first();
                if ($ressource === null) {
                    throw new InvalidArgumentException('Ressource introuvable.');
                }
                if ($this->verifierConflit($ressourceId, $debut, $fin)) {
                    throw new ConflitReservationException(
                        'Cette période chevauche une réservation ou un blocage existant. '
                        . 'Annulez ou déplacez les réservations concernées avant de bloquer.'
                    );
                }

                return $blocages->saveOrFail($blocages->newEntity([
                    'ressource_id' => $ressourceId,
                    'debut' => $debut,
                    'fin' => $fin,
                    'motif' => $motif !== null && trim($motif) !== '' ? trim($motif) : null,
                ]));
            }
        );
    }
}