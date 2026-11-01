<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ConflitReservationException;
use App\Service\ServiceReservation;
use Cake\Http\Exception\ForbiddenException;
use Cake\I18n\DateTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query\SelectQuery;
use InvalidArgumentException;

class BlocagesController extends AppController
{
    public function gestion()
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        $blocages = $this->Blocages->find()
            ->contain(['Ressources'])
            ->innerJoinWith('Ressources', fn (SelectQuery $q) => $q->where(['Ressources.etablissement_id' => $etablissementId]))
            ->orderBy(['Blocages.debut' => 'DESC'])
            ->all();
        $this->set(compact('blocages'));
    }

    public function add()
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        $ressources = $this->fetchTable('Ressources')->find('list', keyField: 'id', valueField: 'nom')
            ->where(['etablissement_id' => $etablissementId])
            ->orderBy(['nom' => 'ASC'])
            ->toArray();
        $erreur = null;

        if ($this->request->is('post')) {
            $donnees = $this->request->getData();
            $ressourceId = (int)($donnees['ressource_id'] ?? 0);
            if (!isset($ressources[$ressourceId])) {
                throw new ForbiddenException('Cette ressource appartient à un autre établissement.');
            }
            try {
                (new ServiceReservation())->bloquerDates(
                    $ressourceId,
                    new DateTime($donnees['debut'] ?? 'now'),
                    new DateTime($donnees['fin'] ?? 'now'),
                    $donnees['motif'] ?? null,
                );
                $this->Flash->success('Période bloquée.');

                return $this->redirect(['action' => 'gestion']);
            } catch (ConflitReservationException | InvalidArgumentException $e) {
                $erreur = $e->getMessage();
            } catch (PersistenceFailedException) {
                $erreur = 'Vérifiez les informations saisies.';
            }
        }

        $this->set(compact('ressources', 'erreur'));
    }

    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $etablissementId = $this->etablissementDuGestionnaire();
        $blocage = $this->Blocages->get((int)$id, contain: ['Ressources']);
        if ((int)$blocage->ressource->etablissement_id !== $etablissementId) {
            throw new ForbiddenException('Ce blocage appartient à un autre établissement.');
        }
        $this->Blocages->delete($blocage);
        $this->Flash->success('Blocage supprimé. La période est de nouveau disponible.');

        return $this->redirect(['action' => 'gestion']);
    }

    private function etablissementDuGestionnaire(): int
    {
        $identite = $this->Authentication->getIdentity();
        $etablissementId = (int)$identite?->get('etablissement_id');
        if ($identite?->get('role') !== 'gestionnaire' || $etablissementId <= 0) {
            throw new ForbiddenException('Accès réservé aux gestionnaires.');
        }

        return $etablissementId;
    }
}