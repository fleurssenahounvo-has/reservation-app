<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ConflitReservationException;
use App\Service\ServiceReservation;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query\SelectQuery;
use InvalidArgumentException;

class ReservationsController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['add', 'confirmation']);
    }

    public function add(?string $ressourceId = null)
    {
        $ressource = $this->fetchTable('Ressources')->get((int)$ressourceId, contain: ['Etablissements']);
        $erreur = null;

        if ($this->request->is('post')) {
            $donnees = $this->request->getData();
            $donnees['ressource_id'] = $ressource->id;
            $donnees['source'] = 'en_ligne';

            if (trim((string)($donnees['telephone_client'] ?? '')) === '') {
                $erreur = 'Le numéro de téléphone est obligatoire.';
            } else {
                try {
                    $reservation = (new ServiceReservation())->reserver($donnees);

                    return $this->redirect(['action' => 'confirmation', $reservation->id]);
                } catch (ConflitReservationException | InvalidArgumentException $e) {
                    $erreur = $e->getMessage();
                } catch (PersistenceFailedException) {
                    $erreur = 'Vérifiez vos informations (adresse e-mail invalide ?).';
                }
            }
        }

        $this->set(compact('ressource', 'erreur'));
    }

    public function confirmation(?string $id = null)
    {
        $reservation = $this->Reservations->get((int)$id, contain: ['Ressources' => ['Etablissements']]);
        $this->set(compact('reservation'));
    }

    /**
     * Réservations de l'établissement du gestionnaire connecté.
     */
    public function gestion()
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        (new ServiceReservation())->expirerEnAttente();

        $statut = (string)$this->request->getQuery('statut', '');
        $query = $this->Reservations->find()
            ->contain(['Ressources'])
            ->innerJoinWith('Ressources', fn (SelectQuery $q) => $q->where(['Ressources.etablissement_id' => $etablissementId]))
            ->orderBy(['Reservations.debut' => 'DESC']);
        if (in_array($statut, ['en_attente', 'confirmee', 'annulee', 'expiree'], true)) {
            $query->where(['Reservations.statut' => $statut]);
        }

        $reservations = $this->paginate($query);
        $this->set(compact('reservations', 'statut'));
    }

    public function confirmer(?string $id = null)
    {
        $this->request->allowMethod(['post']);
        $reservation = $this->reservationDuGestionnaire((int)$id);
        try {
            (new ServiceReservation())->confirmer($reservation->id);
            $this->Flash->success('Réservation confirmée.');
        } catch (InvalidArgumentException $e) {
            $this->Flash->error($e->getMessage());
        }

        return $this->redirect(['action' => 'gestion']);
    }

    public function annuler(?string $id = null)
    {
        $this->request->allowMethod(['post']);
        $reservation = $this->reservationDuGestionnaire((int)$id);
        try {
            (new ServiceReservation())->annuler($reservation->id);
            $this->Flash->success('Réservation annulée. Le créneau est de nouveau libre.');
        } catch (InvalidArgumentException $e) {
            $this->Flash->error($e->getMessage());
        }

        return $this->redirect(['action' => 'gestion']);
    }

    /**
     * Numéro de l'établissement du gestionnaire connecté, sinon accès refusé.
     */
    private function etablissementDuGestionnaire(): int
    {
        $identite = $this->Authentication->getIdentity();
        $etablissementId = (int)$identite?->get('etablissement_id');
        if ($identite?->get('role') !== 'gestionnaire' || $etablissementId <= 0) {
            throw new ForbiddenException('Accès réservé aux gestionnaires.');
        }

        return $etablissementId;
    }

    /**
     * Charge une réservation, uniquement si elle appartient à l'établissement du gestionnaire.
     */
    private function reservationDuGestionnaire(int $id)
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        $reservation = $this->Reservations->get($id, contain: ['Ressources']);
        if ((int)$reservation->ressource->etablissement_id !== $etablissementId) {
            throw new ForbiddenException('Cette réservation appartient à un autre établissement.');
        }

        return $reservation;
    }
}