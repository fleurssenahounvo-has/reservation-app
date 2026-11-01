<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ServiceReservation;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\I18n\DateTime;

class RessourcesController extends AppController
{
    private const TYPES = [
        'conference' => 'Salle de conférence',
        'reception' => 'Salle de réception',
        'table' => 'Table de restaurant',
    ];

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'view']);
    }

    public function index()
    {
        $query = $this->Ressources->find()->contain(['Etablissements']);

        $type = $this->request->getQuery('type');
        if (!empty($type)) {
            $query->where(['Ressources.type' => $type]);
        }
        $capacite = (int)$this->request->getQuery('capacite');
        if ($capacite > 0) {
            $query->where(['Ressources.capacite >=' => $capacite]);
        }

        $ressources = $this->paginate($query);
        $this->set(compact('ressources'));
    }

    public function view(?string $id = null)
    {
        $ressource = $this->Ressources->get((int)$id, contain: ['Etablissements']);
        $service = new ServiceReservation();

        $mois = (string)$this->request->getQuery('mois', DateTime::now()->format('Y-m'));
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mois)) {
            $mois = DateTime::now()->format('Y-m');
        }
        [$annee, $numMois] = array_map('intval', explode('-', $mois));
        $premier = new DateTime(sprintf('%04d-%02d-01', $annee, $numMois));
        $jours = $service->disponibilitesMois($ressource->id, $annee, $numMois);

        $jourChoisi = null;
        $creneaux = [];
        $jourParam = (string)$this->request->getQuery('jour', '');
        if (
            preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $jourParam, $m)
            && substr($jourParam, 0, 7) === $mois
            && checkdate((int)$m[2], (int)$m[3], (int)$m[1])
        ) {
            $jourChoisi = new DateTime($jourParam);
            $creneaux = $service->creneauxJour($ressource->id, $jourChoisi);
        }

        $precedent = $premier->subMonths(1)->format('Y-m');
        $suivant = $premier->addMonths(1)->format('Y-m');
        $this->set(compact('ressource', 'jours', 'premier', 'mois', 'precedent', 'suivant', 'jourChoisi', 'creneaux'));
    }

    /**
     * Ressources de l'établissement du gestionnaire connecté.
     */
    public function gestion()
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        $ressources = $this->Ressources->find()
            ->where(['etablissement_id' => $etablissementId])
            ->orderBy(['nom' => 'ASC'])
            ->all();
        $this->set(compact('ressources'));
        $this->set('types', self::TYPES);
    }

    public function add()
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        $ressource = $this->Ressources->newEmptyEntity();

        if ($this->request->is('post')) {
            $donnees = array_intersect_key($this->request->getData(), array_flip(['nom', 'type', 'capacite', 'prix']));
            $ressource = $this->Ressources->patchEntity($ressource, $donnees);
            $ressource->etablissement_id = $etablissementId;
            if ($this->Ressources->save($ressource)) {
                $this->Flash->success('Ressource ajoutée.');

                return $this->redirect(['action' => 'gestion']);
            }
            $this->Flash->error("La ressource n'a pas pu être enregistrée. Vérifiez les champs.");
        }

        $this->set(compact('ressource'));
        $this->set('types', self::TYPES);

        return $this->render('formulaire');
    }

    public function edit(?string $id = null)
    {
        $ressource = $this->ressourceDuGestionnaire((int)$id);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $donnees = array_intersect_key($this->request->getData(), array_flip(['nom', 'type', 'capacite', 'prix']));
            $ressource = $this->Ressources->patchEntity($ressource, $donnees);
            if ($this->Ressources->save($ressource)) {
                $this->Flash->success('Ressource modifiée.');

                return $this->redirect(['action' => 'gestion']);
            }
            $this->Flash->error("La ressource n'a pas pu être enregistrée. Vérifiez les champs.");
        }

        $this->set(compact('ressource'));
        $this->set('types', self::TYPES);

        return $this->render('formulaire');
    }

    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ressource = $this->ressourceDuGestionnaire((int)$id);

        $enCours = $this->fetchTable('Reservations')->find()
            ->where([
                'ressource_id' => $ressource->id,
                'statut IN' => ServiceReservation::STATUTS_ACTIFS,
                'fin >' => DateTime::now(),
            ])
            ->count();
        if ($enCours > 0) {
            $this->Flash->error("Impossible de supprimer : {$enCours} réservation(s) à venir. Annulez-les d'abord.");

            return $this->redirect(['action' => 'gestion']);
        }

        if ($this->Ressources->delete($ressource)) {
            $this->Flash->success('Ressource supprimée.');
        } else {
            $this->Flash->error("La ressource n'a pas pu être supprimée.");
        }

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

    private function ressourceDuGestionnaire(int $id)
    {
        $etablissementId = $this->etablissementDuGestionnaire();
        $ressource = $this->Ressources->get($id);
        if ((int)$ressource->etablissement_id !== $etablissementId) {
            throw new ForbiddenException('Cette ressource appartient à un autre établissement.');
        }

        return $ressource;
    }
}