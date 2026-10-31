<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ConflitReservationException;
use App\Service\ServiceReservation;
use Cake\ORM\Exception\PersistenceFailedException;
use InvalidArgumentException;

class ReservationsController extends AppController
{
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
}